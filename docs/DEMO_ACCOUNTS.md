# Local demo accounts

Run the account seeder with:

```powershell
php artisan db:seed --class=DemoAccountsSeeder
```

This seeder refuses to run when the application environment is `production`.

| Role | Email | Password | Landing page |
|---|---|---|---|
| Super administrator | `superadmin@example.com` | `Demo@12345` | Platform dashboard |
| Organizer | `organizer@example.com` | `Demo@12345` | Organizer dashboard |
| Security officer | `security@example.com` | `Demo@12345` | QR scanner |

The security officer is automatically attached to all events owned by the demo organizer. Running the seeder again updates the same accounts rather than creating duplicates.
