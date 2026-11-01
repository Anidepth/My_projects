using ExpenseTracker.Data;
using ExpenseTracker.Models;
using Microsoft.EntityFrameworkCore;

namespace ExpenseTracker.Services;

/// <summary>
/// Per-circuit (per browser session) holder of the current user's appearance/currency
/// settings. Loaded from SQLite on first use (keyed by the logged-in user's id); changes
/// are applied live to subscribers via <see cref="OnChange"/> and written back via
/// <see cref="SaveAsync"/>.
/// </summary>
public class AppSettingsService
{
    private readonly IDbContextFactory<AppDbContext> _dbFactory;
    private readonly CurrentUserService _currentUser;
    private bool _loaded;
    private int _userId;

    public AppSettingsService(IDbContextFactory<AppDbContext> dbFactory, CurrentUserService currentUser)
    {
        _dbFactory = dbFactory;
        _currentUser = currentUser;
    }

    public string ColorTheme { get; private set; } = "Default";

    /// <summary>The one and only dark-mode switch: off = light, on = pure black.</summary>
    public bool PureBlackDarkMode { get; private set; }

    public int FontSize { get; private set; } = 15;
    public string Currency { get; private set; } = "NPR";

    /// <summary>"light" or "dark", derived directly from PureBlackDarkMode.</summary>
    public string Mode => PureBlackDarkMode ? "dark" : "light";

    public event Action? OnChange;

    public async Task EnsureLoadedAsync()
    {
        if (_loaded) return;
        _userId = await _currentUser.GetUserIdAsync();

        await using var db = await _dbFactory.CreateDbContextAsync();
        var entity = await db.Settings.FindAsync(_userId);
        if (entity is null)
        {
            entity = new AppSettingsEntity { Id = _userId };
            db.Settings.Add(entity);
            await db.SaveChangesAsync();
        }

        ColorTheme = entity.ColorTheme;
        PureBlackDarkMode = entity.PureBlackDarkMode;
        FontSize = entity.FontSize;
        Currency = entity.Currency;
        _loaded = true;
    }

    /// <summary>Applies (and broadcasts) a full set of appearance choices, e.g. from the Settings page.</summary>
    public void ApplyLocal(string colorTheme, bool pureBlack, int fontSize, string currency)
    {
        ColorTheme = colorTheme;
        PureBlackDarkMode = pureBlack;
        FontSize = fontSize;
        Currency = currency;
        OnChange?.Invoke();
    }

    /// <summary>Quick currency change (e.g. from the sidebar shortcut) — applies and persists immediately.</summary>
    public async Task SetCurrencyAsync(string currency)
    {
        Currency = currency;
        OnChange?.Invoke();
        await SaveAsync();
    }

    public async Task SaveAsync()
    {
        await using var db = await _dbFactory.CreateDbContextAsync();
        var entity = await db.Settings.FindAsync(_userId);
        if (entity is null)
        {
            entity = new AppSettingsEntity { Id = _userId };
            db.Settings.Add(entity);
        }

        entity.ColorTheme = ColorTheme;
        entity.PureBlackDarkMode = PureBlackDarkMode;
        entity.FontSize = FontSize;
        entity.Currency = Currency;
        await db.SaveChangesAsync();
    }
}
