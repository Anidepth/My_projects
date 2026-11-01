namespace ExpenseTracker.Models;

/// <summary>Single-row table holding the user's appearance/currency preferences.</summary>
public class AppSettingsEntity
{
    public int Id { get; set; } = 1;
    public string ColorTheme { get; set; } = "Default";     // Default | Dynamic | GreenApple | Lavender | Midnight
    public bool PureBlackDarkMode { get; set; } = false;     // off = light, on = pure black
    public int FontSize { get; set; } = 15;
    public string Currency { get; set; } = "NPR";
}

public static class ColorThemes
{
    public static readonly (string Key, string Label)[] All =
    {
        ("Default", "Default"),
        ("Dynamic", "Dynamic"),
        ("GreenApple", "Green Apple"),
        ("Lavender", "Lavender"),
        ("Midnight", "Midnight"),
    };
}

public static class Currencies
{
    public static readonly (string Code, string Symbol, string Label)[] All =
    {
        ("NPR", "NRs.", "Nepalese Rupee (NPR)"),
        ("INR", "Rs.", "Indian Rupee (INR)"),
        ("USD", "$", "US Dollar (USD)"),
        ("EUR", "€", "Euro (EUR)"),
        ("GBP", "£", "British Pound (GBP)"),
        ("JPY", "¥", "Japanese Yen (JPY)"),
        ("AUD", "A$", "Australian Dollar (AUD)"),
        ("CAD", "C$", "Canadian Dollar (CAD)"),
    };

    public static string SymbolFor(string code) =>
        All.FirstOrDefault(c => c.Code == code).Symbol is { Length: > 0 } s ? s : code;
}

/// <summary>
/// Formats an amount that is stored in the base currency (NPR), converting it to the
/// user's selected display currency first — not just swapping the symbol.
/// </summary>
public static class MoneyFormat
{
    public static string Format(decimal amountInBaseCurrency, string displayCurrencyCode)
    {
        var converted = ExchangeRates.ConvertFromBase(amountInBaseCurrency, displayCurrencyCode);
        var symbol = Currencies.SymbolFor(displayCurrencyCode);
        return $"{symbol} {converted:N2}";
    }
}
