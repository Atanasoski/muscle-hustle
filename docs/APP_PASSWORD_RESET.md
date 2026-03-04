# App: Forgot Password & Reset Password

This document describes how the mobile/frontend app should implement **forgot password** and **reset password** so it works with this backend’s API.

## Flow overview

1. User taps **Forgot password?** → app shows email screen → app calls **POST /api/forgot-password**.
2. Backend sends a password reset email to the user (same email as web).
3. User opens the link in the email (web page or app deep link).
4. User enters new password (and confirmation) in the app or on the web page.
5. App calls **POST /api/reset-password** with token, email, and new password.
6. On success, app shows a success message and navigates to the **login** screen.

---

## Step 1 – Request a password reset link (“Forgot password?”)

### UI

- Show a screen (or modal) with a single field: **email**.
- Submit button: e.g. “Send reset link” or “Email me a reset link”.

### API call

- **Method:** `POST`
- **URL:** `{BASE_URL}/api/forgot-password`  
  Example: `https://api.yourdomain.com/api/forgot-password`
- **Headers:**
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Body:**
  ```json
  {
    "email": "user@example.com"
  }
  ```

### Responses

- **Success (200)**  
  Body: `{ "message": "We have emailed your password reset link." }` (or similar from backend)  
  **App:** Show a single, generic message such as:  
  *“If an account exists for this email, we’ve sent a password reset link.”*  
  Do **not** show different text for “email found” vs “email not found” (avoids email enumeration).

- **Validation error (422)**  
  Body includes validation errors, e.g. `email` required or invalid format.  
  **App:** Show the error message(s) from the response (e.g. “The email field is required.” / “The email must be a valid email address.”).

- **Other (422)**  
  Backend may return a generic message (e.g. user not found).  
  **App:** Still show the same generic success message as for 200, or a neutral message like “If an account exists, we’ve sent a reset link.” Do not reveal whether the email exists.

---

## Step 2 – User receives the email

- The backend sends the same Laravel reset email as the web.
- The link looks like:  
  `https://your-domain.com/reset-password/{token}?email=user%40example.com`

### Option A – Web-only reset

- User taps the link and completes the reset in the **browser** (existing web page).
- No extra app logic needed. After reset, user can open the app and log in with the new password.

### Option B – In-app reset (better UX)

- **Backend (optional):** The reset email can be customized so the link is an **app deep link**, e.g.  
  `yourapp://reset-password?token=...&email=...`
- **App:**  
  - Register a deep link / universal link for a path like `reset-password`.  
  - When the app opens from that link, parse `token` and `email` from the query.  
  - Show an in-app “New password” screen (password + confirm password).  
  - On submit, call the reset API (Step 3). The user does not need to copy the token.

---

## Step 3 – Submit the new password (reset password)

### When to show this screen

- User landed in the app via the reset link (Option B), **or**
- User manually opened the app and you have stored/collected the `token` and `email` (e.g. from a custom in-app link or re-entry flow).

### API call

- **Method:** `POST`
- **URL:** `{BASE_URL}/api/reset-password`
- **Headers:**
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Body:**
  ```json
  {
    "token": "<from reset link or your stored value>",
    "email": "user@example.com",
    "password": "<new password>",
    "password_confirmation": "<same as password>"
  }
  ```

### Responses

- **Success (200)**  
  Body: `{ "message": "Your password has been reset." }` (or similar)  
  **App:** Show “Your password has been reset.” (or similar) and **navigate to the login screen** so the user can sign in with the new password.

- **Error (422)**  
  - Invalid or expired token: backend returns a message like “This password reset token is invalid.”  
    **App:** Show that message and offer “Request a new link” (which takes the user back to the forgot-password screen).  
  - Validation errors (e.g. password too short, passwords don’t match): backend returns validation errors.  
    **App:** Show the returned error message(s).

---

## App checklist

- Use **`Accept: application/json`** and **`Content-Type: application/json`** on both endpoints so the backend always returns JSON.
- After **forgot password**, show **one generic success message**; do not differentiate between “email exists” and “email does not exist”.
- **Validate password** in the app (length, match) before calling the reset API to reduce failed requests; the backend will still validate and may return 422.
- **Reset tokens expire** (Laravel default: 60 minutes). If the user opens the link late, show the backend’s error and offer “Request a new link”.

---

## Quick reference

| Step | Endpoint | Method | Body |
|------|----------|--------|------|
| Forgot password | `/api/forgot-password` | POST | `{ "email": "..." }` |
| Reset password | `/api/reset-password` | POST | `{ "token", "email", "password", "password_confirmation" }` |

Both endpoints are **public** (no auth header required). Use your app’s `BASE_URL` (e.g. `https://api.yourdomain.com`) for all requests.
