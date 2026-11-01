export async function logoutWithToken() {
    // Fetch request token (this will also set the antiforgery cookie)
    const tokenResp = await fetch('/account/antiforgery-token', { credentials: 'same-origin' });
    if (!tokenResp.ok) throw new Error('Failed to get antiforgery token');
    const tokenJson = await tokenResp.json();
    const token = tokenJson.requestToken;

    // POST to logout with the token in the header
    const resp = await fetch('/account/logout', {
        method: 'POST',
        headers: { 'RequestVerificationToken': token },
        credentials: 'same-origin'
    });

    // Redirect to login (server also redirects, but this ensures client navigates)
    window.location.href = '/login';
}
