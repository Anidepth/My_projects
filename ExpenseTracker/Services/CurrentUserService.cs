using System.Security.Claims;
using Microsoft.AspNetCore.Components.Authorization;

namespace ExpenseTracker.Services;

public class CurrentUserService
{
    private readonly AuthenticationStateProvider _authStateProvider;
    private int? _userId;

    public CurrentUserService(AuthenticationStateProvider authStateProvider)
    {
        _authStateProvider = authStateProvider;
    }

    public async Task<int> GetUserIdAsync()
    {
        if (_userId is not null) return _userId.Value;

        var state = await _authStateProvider.GetAuthenticationStateAsync();
        var claim = state.User.FindFirst(ClaimTypes.NameIdentifier);
        _userId = claim is not null && int.TryParse(claim.Value, out var id) ? id : 0;
        return _userId.Value;
    }
}
