using System.ComponentModel.DataAnnotations;

namespace ExpenseTracker.Models;

public class Expense
{
    public int Id { get; set; }

    public int UserId { get; set; }

    [Required(ErrorMessage = "Give it a short description.")]
    [StringLength(120)]
    public string Description { get; set; } = string.Empty;

    [Range(0.01, 1_000_000, ErrorMessage = "Amount must be greater than 0.")]
    public decimal Amount { get; set; }

    [Required]
    public string Category { get; set; } = "Other";

    public DateTime Date { get; set; } = DateTime.Today;

    public string PaymentMethod { get; set; } = "Card";

    [StringLength(300)]
    public string? Notes { get; set; }
}

public static class ExpenseCategories
{
    public static readonly string[] All =
    {
        "Food & Dining", "Transportation", "Housing", "Utilities",
        "Entertainment", "Shopping", "Health", "Travel", "Education", "Other"
    };
}

public static class PaymentMethods
{
    public static readonly string[] All = { "Card", "Cash", "Bank Transfer", "Digital Wallet" };
}
