using System.Security.Claims;
using ExpenseTracker.Components;
using ExpenseTracker.Data;
using ExpenseTracker.Models;
using ExpenseTracker.Services;
using Microsoft.AspNetCore.Authentication;
using Microsoft.AspNetCore.Authentication.Cookies;
using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.AspNetCore.Antiforgery;
using Microsoft.EntityFrameworkCore;

var builder = WebApplication.CreateBuilder(args);

builder.Services.AddRazorComponents()
    .AddInteractiveServerComponents();

builder.Services.AddDbContextFactory<AppDbContext>(options =>
    options.UseSqlite(builder.Configuration.GetConnectionString("Default") ?? "Data Source=expenses.db"));

builder.Services.AddHttpContextAccessor();
builder.Services.AddScoped<CurrentUserService>();
builder.Services.AddScoped<AppSettingsService>();

builder.Services.AddAuthentication(CookieAuthenticationDefaults.AuthenticationScheme)
    .AddCookie(options =>
    {
        options.LoginPath = "/login";
        options.AccessDeniedPath = "/login";
        options.ExpireTimeSpan = TimeSpan.FromDays(30);
        options.SlidingExpiration = true;
    });
builder.Services.AddAuthorization();
builder.Services.AddCascadingAuthenticationState();
builder.Services.AddAntiforgery();
builder.Services.AddHttpClient();

var app = builder.Build();

if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Error", createScopeForErrors: true);
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseAuthentication();
app.UseAuthorization();
app.UseAntiforgery();

app.MapRazorComponents<App>()
    .AddInteractiveServerRenderMode();

// --- Auth endpoints -----------------------------------------------------
// These are plain HTTP endpoints (not Blazor components), so a normal form
// POST reaches them directly and a Set-Cookie header can actually be written
// on the response. Blazor's interactive circuit runs over a long-lived
// SignalR connection where that isn't reliably possible, which is why login
// and registration are handled here instead of inside a razor component.

app.MapPost("/account/login", async (HttpContext http, IDbContextFactory<AppDbContext> dbFactory) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    var form = await http.Request.ReadFormAsync();
    var username = form["username"].ToString().Trim();
    var password = form["password"].ToString();
    var returnUrl = form["returnUrl"].ToString();

    await using var db = await dbFactory.CreateDbContextAsync();
    var user = await db.Users.FirstOrDefaultAsync(u => u.Username == username);

    if (user is null || !PasswordHasher.Verify(password, user.PasswordHash, user.PasswordSalt))
    {
        var target = "/login?error=1" + (string.IsNullOrEmpty(returnUrl) ? "" : $"&returnUrl={Uri.EscapeDataString(returnUrl)}");
        return Results.Redirect(target);
    }

    await SignInAsync(http, user);
    return Results.Redirect(string.IsNullOrEmpty(returnUrl) ? "/" : returnUrl);
});

app.MapPost("/account/register", async (HttpContext http, IDbContextFactory<AppDbContext> dbFactory) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    var form = await http.Request.ReadFormAsync();
    var username = form["username"].ToString().Trim();
    var email = form["email"].ToString().Trim();
    var password = form["password"].ToString();
    var confirm = form["confirmPassword"].ToString();

    await using var db = await dbFactory.CreateDbContextAsync();

    string? error =
        username.Length < 3 ? "Username must be at least 3 characters." :
        password.Length < 6 ? "Password must be at least 6 characters." :
        password != confirm ? "Passwords don't match." :
        await db.Users.AnyAsync(u => u.Username == username) ? "That username is already taken." :
        null;

    if (error is not null)
    {
        return Results.Redirect($"/register?error={Uri.EscapeDataString(error)}");
    }

    var (hash, salt) = PasswordHasher.Hash(password);
    var user = new User { Username = username, Email = email, PasswordHash = hash, PasswordSalt = salt };
    db.Users.Add(user);
    await db.SaveChangesAsync();

    // Do not automatically sign in after registration. Redirect to login so the
    // user must explicitly authenticate (helps prevent accidental auto-login).
    return Results.Redirect("/login?registered=1");
});

app.MapPost("/account/logout", async (HttpContext http) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    await http.SignOutAsync(CookieAuthenticationDefaults.AuthenticationScheme);
    return Results.Redirect("/login");
});

app.MapPost("/account/update-username", async (HttpContext http, IDbContextFactory<AppDbContext> dbFactory) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    var userId = CurrentUserId(http);
    if (userId is null) return Results.Redirect("/login");

    var form = await http.Request.ReadFormAsync();
    var newUsername = form["username"].ToString().Trim();

    await using var db = await dbFactory.CreateDbContextAsync();

    string? error =
        newUsername.Length < 3 ? "Username must be at least 3 characters." :
        await db.Users.AnyAsync(u => u.Username == newUsername && u.Id != userId) ? "That username is already taken." :
        null;

    if (error is not null)
    {
        return Results.Redirect($"/settings?error={Uri.EscapeDataString(error)}");
    }

    var user = await db.Users.FindAsync(userId.Value);
    if (user is null) return Results.Redirect("/login");

    user.Username = newUsername;
    await db.SaveChangesAsync();

    // Re-issue the auth cookie so the sidebar's displayed username updates immediately.
    await SignInAsync(http, user);
    return Results.Redirect("/settings?success=username");
}).RequireAuthorization();

app.MapPost("/account/update-email", async (HttpContext http, IDbContextFactory<AppDbContext> dbFactory) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    var userId = CurrentUserId(http);
    if (userId is null) return Results.Redirect("/login");

    var form = await http.Request.ReadFormAsync();
    var newEmail = form["email"].ToString().Trim();

    if (!string.IsNullOrEmpty(newEmail) && !newEmail.Contains('@'))
    {
        return Results.Redirect($"/settings?error={Uri.EscapeDataString("That doesn't look like a valid email.")}");
    }

    await using var db = await dbFactory.CreateDbContextAsync();
    var user = await db.Users.FindAsync(userId.Value);
    if (user is null) return Results.Redirect("/login");

    user.Email = newEmail;
    await db.SaveChangesAsync();

    return Results.Redirect("/settings?success=email");
}).RequireAuthorization();

app.MapPost("/account/change-password", async (HttpContext http, IDbContextFactory<AppDbContext> dbFactory) =>
{
    var antiforgery = http.RequestServices.GetRequiredService<IAntiforgery>();
    await antiforgery.ValidateRequestAsync(http);

    var userId = CurrentUserId(http);
    if (userId is null) return Results.Redirect("/login");

    var form = await http.Request.ReadFormAsync();
    var currentPassword = form["currentPassword"].ToString();
    var newPassword = form["newPassword"].ToString();
    var confirmPassword = form["confirmPassword"].ToString();

    await using var db = await dbFactory.CreateDbContextAsync();
    var user = await db.Users.FindAsync(userId.Value);
    if (user is null) return Results.Redirect("/login");

    string? error =
        !PasswordHasher.Verify(currentPassword, user.PasswordHash, user.PasswordSalt) ? "Current password is incorrect." :
        newPassword.Length < 6 ? "New password must be at least 6 characters." :
        newPassword != confirmPassword ? "New passwords don't match." :
        null;

    if (error is not null)
    {
        return Results.Redirect($"/settings?error={Uri.EscapeDataString(error)}");
    }

    var (hash, salt) = PasswordHasher.Hash(newPassword);
    user.PasswordHash = hash;
    user.PasswordSalt = salt;
    await db.SaveChangesAsync();

    return Results.Redirect("/settings?success=password");
}).RequireAuthorization();

// Return an antiforgery request token (and set the cookie token). The browser
// should call this immediately before POSTing to a protected endpoint so the
// cookie and request tokens are generated together and will match.
app.MapGet("/account/antiforgery-token", (HttpContext http, IAntiforgery antiforgery) =>
{
    var tokens = antiforgery.GetAndStoreTokens(http);
    return Results.Json(new { requestToken = tokens.RequestToken });
});

// Ensure the SQLite database and schema exist on startup.
using (var scope = app.Services.CreateScope())
{
    var dbFactory = scope.ServiceProvider.GetRequiredService<IDbContextFactory<AppDbContext>>();
    await using var db = await dbFactory.CreateDbContextAsync();
    await db.Database.EnsureCreatedAsync();
}

app.Run();

static int? CurrentUserId(HttpContext http)
{
    var claim = http.User.FindFirst(ClaimTypes.NameIdentifier)?.Value;
    return claim is not null && int.TryParse(claim, out var id) ? id : null;
}

static async Task SignInAsync(HttpContext http, User user)
{
    var claims = new List<Claim>
    {
        new(ClaimTypes.NameIdentifier, user.Id.ToString()),
        new(ClaimTypes.Name, user.Username),
    };
    var identity = new ClaimsIdentity(claims, CookieAuthenticationDefaults.AuthenticationScheme);
    var principal = new ClaimsPrincipal(identity);
    await http.SignInAsync(CookieAuthenticationDefaults.AuthenticationScheme, principal,
        new AuthenticationProperties { IsPersistent = false });
}
