namespace ExpenseTracker.Models;

/// <summary>
/// All expense amounts are stored internally in Nepalese Rupees (NPR) — the app's base
/// currency. "Display currency" in Settings just converts for viewing/entry, using these
/// approximate rates. They are static (not live market rates) — tweak them here if you
/// want closer-to-real numbers.
/// </summary>
public static class ExchangeRates
{
    public const string BaseCurrency = "NPR";

    // Units of the target currency equal to 1 NPR. E.g. USD ≈ 1/153 means ~153 NPR = 1 USD.
    private static readonly Dictionary<string, decimal> RatesPerNpr = new()
    {
        ["NPR"] = 1m,
        ["INR"] = 0.625m,        // 1 INR = 1.6 NPR (fixed peg)
        ["USD"] = 1m / 153m,     // ~153 NPR = 1 USD
        ["EUR"] = 1m / 165m,     // ~165 NPR = 1 EUR
        ["GBP"] = 1m / 195m,     // ~195 NPR = 1 GBP
        ["JPY"] = 150m / 153m,   // ~1.02 JPY per NPR
        ["AUD"] = 1.5m / 153m,   // ~102 NPR = 1 AUD
        ["CAD"] = 1.35m / 153m,  // ~113 NPR = 1 CAD
    };

    /// <summary>Converts an amount stored in the base currency (NPR) into <paramref name="targetCurrency"/>.</summary>
    public static decimal ConvertFromBase(decimal amountInNpr, string targetCurrency)
    {
        var rate = RatesPerNpr.TryGetValue(targetCurrency, out var r) ? r : 1m;
        return amountInNpr * rate;
    }

    /// <summary>Converts an amount the user typed in <paramref name="sourceCurrency"/> back into the base currency (NPR).</summary>
    public static decimal ConvertToBase(decimal amountInSourceCurrency, string sourceCurrency)
    {
        var rate = RatesPerNpr.TryGetValue(sourceCurrency, out var r) && r != 0 ? r : 1m;
        return amountInSourceCurrency / rate;
    }
}
