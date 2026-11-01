# Ledger — Expense Tracker

A small, self-contained expense tracker built with **.NET 8 Blazor Server** and **EF Core + SQLite**.
Full CRUD (create, read, update, delete) on expenses, plus a dashboard with monthly totals and a
category breakdown. No JavaScript framework, no external UI kit — the look is hand-styled in
`wwwroot/css/app.css`.

## Accounts

The app now requires logging in. Visit any page while signed out and you'll be redirected to
`/login`; from there you can either log in or hit "Create new account" to register. Each account
has its own expenses, and its own appearance/currency settings.

- Passwords are hashed with PBKDF2 (100,000 iterations, SHA-256) — never stored in plain text.
- Sessions use a standard ASP.NET Core auth cookie (persists 30 days, sliding expiration).
- Login/Register are plain HTTP form posts handled by two small endpoints in `Program.cs`
  (`/account/login`, `/account/register`, `/account/logout`) rather than inside a Blazor
  component — that's intentional. Blazor Server's interactive pages run over a long-lived
  SignalR connection, which can't reliably set cookies; a real HTTP POST can.

**If you already ran a previous version of this app**, delete `expenses.db` before running this
one — the database schema changed (new `Users` table, and expenses/settings are now scoped per
account), and this app uses `EnsureCreated` rather than migrations, so it won't auto-upgrade an
existing file.

## Requirements

- [.NET 8 SDK](https://dotnet.microsoft.com/download/dotnet/8.0) installed on your machine
  (this project was written and reviewed in a sandbox without the SDK installed, so **run it
  locally to build/launch it** — see below for what to check if something doesn't compile).

## Run it

```bash
cd ExpenseTracker
dotnet restore
dotnet run
```

Then open the URL printed in the console (something like `https://localhost:5001` or
`http://localhost:5000`). A `expenses.db` SQLite file is created automatically on first run —
no separate database setup needed.

## What's inside

```
ExpenseTracker/
├── Program.cs                     # app startup, EF Core + Blazor wiring
├── Data/AppDbContext.cs           # EF Core DbContext (SQLite)
├── Models/Expense.cs              # Expense entity + category/payment lookups
├── Components/
│   ├── App.razor                  # HTML shell, fonts
│   ├── Routes.razor               # router
│   ├── Layout/
│   │   ├── MainLayout.razor       # sidebar + content shell
│   │   └── NavMenu.razor          # sidebar navigation
│   └── Pages/
│       ├── Dashboard.razor        # "/" — monthly totals, category bars, recent activity
│       ├── Expenses.razor         # "/expenses" — full CRUD table + add/edit drawer
│       └── Error.razor
└── wwwroot/css/app.css            # the entire visual design, hand-written
```

## CRUD

All of it lives in `Components/Pages/Expenses.razor`:

- **Create** — "+ Add expense" opens a slide-over drawer form (validated with data annotations).
- **Read** — table with live search (description/notes) and category filter.
- **Update** — the pencil icon opens the same drawer pre-filled.
- **Delete** — the trash icon opens a confirmation dialog before removing the row.

Data access goes through `IDbContextFactory<AppDbContext>`, which is the recommended pattern for
Blazor Server so each operation gets its own short-lived `DbContext` instead of one shared,
long-lived instance.

## Design notes

The UI intentionally avoids the generic "AI app" look (Inter font, purple/blue gradient, heavy
drop shadows on everything). Instead:

- **Fraunces** (serif) for headings/brand, **Public Sans** for body text, **IBM Plex Mono** for
  numbers — gives it a "ledger" feel rather than a SaaS-dashboard feel.
- Warm paper background (`#f6f2e9`) with a deep forest-green sidebar and a terracotta accent
  instead of blue/violet.
- Flat borders and 1px lines rather than heavy card shadows; a slide-over drawer instead of a
  modal for add/edit.

Feel free to tweak the CSS variables at the top of `app.css` (`--paper`, `--accent`, `--forest`,
fonts) to retheme it without touching any component markup.

## If something doesn't build

This was written carefully but wasn't compiled in this environment (no .NET SDK / NuGet access
in the sandbox that generated it). If `dotnet restore`/`dotnet run` hits an error:

1. Confirm `dotnet --version` reports 8.x.
2. Delete `bin/` and `obj/` and retry `dotnet restore`.
3. Paste the exact error back and it can be fixed quickly — the codebase is small (7 `.razor`
   files, 1 model, 1 DbContext, 1 `Program.cs`).
