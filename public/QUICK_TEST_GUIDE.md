# Quick Test Script for Auction APIs

## 🔐 Login Test
⚠️ **IMPORTANT**: Must use POST method, not GET!

```bash
curl -X POST "https://testsites.azsystems.tech/api/login" \
  -H "Accept: application/json" \
  -F "email=azsystems@ajwaaalsallmah.com" \
  -F "password=Aa123456789" \
  -F "remember_me=1" \
  -k
```

**Expected Response:**
```json
{
  "message": "Login successful | تم تسجيل الدخول بنجاح",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {...}
}
```

## 📋 Get Active Auctions
```bash
curl -X GET "https://testsites.azsystems.tech/api/auctions/active" \
  -H "Accept: application/json" \
  -k
```

## 🔨 Activate Auction (with token)
```bash
TOKEN="YOUR_JWT_TOKEN_HERE"
curl -X PATCH "https://testsites.azsystems.tech/api/auctions/19/activate" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -k
```

## 💰 Place Bid (with token)
```bash
TOKEN="YOUR_JWT_TOKEN_HERE"
curl -X POST "https://testsites.azsystems.tech/api/auctions/19/bid" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"bid_amount": 2100}' \
  -k
```

## 📊 Working JWT Token (for testing):
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3Rlc3RzaXRlcy5henN5c3RlbXMudGVjaC9hcGkvbG9naW4iLCJpYXQiOjE3NTkxMDkwNzIsImV4cCI6MTc5MDY0NTA3MiwibmJmIjoxNzU5MTA5MDcyLCJqdGkiOiJzOW5Qck9Td2I0ZTJFVVpsIiwic3ViIjoiMTAxIiwicHJ2IjoiYTk3NWI1MGRkOTE0NTRhM2Y1YzNhNTU1ZTZiZjdmM2U3OTY2ZTdjNCJ9.lZmQR6IgbRw0G1w5Qbx2ohyqUVEXIKBLTqolRb6R8nw
```

## 🏃‍♂️ Quick Full Test Flow:
1. Login → Get token
2. Get active auctions → See current status
3. Activate auction (if needed) → Make auction active
4. Place bid → Test bidding functionality
5. Get auction details → Verify results

## 📱 Postman Collections Available:
- **`Auction_APIs_FormData.postman_collection.json`** ← **Best for your use case**
- **`Complete_Auction_APIs.postman_collection.json`** 
- **`Auction_Environment.postman_environment.json`**

## 🔧 Correct Login Data:
- **Email**: `azsystems@ajwaaalsallmah.com`
- **Password**: `Aa123456789`
- **Remember Me**: `1` (not `true`)
- **HTTP Method**: `POST` (not GET) ⚠️
- **Body Type**: Form-Data (not JSON)

## ❌ Common Errors:
1. **"GET method not supported"** → Use POST method
2. **"remember_me must be true or false"** → Use `1` instead of `true`
3. **"301 Moved Permanently"** → Use HTTPS instead of HTTP

## 📍 URLs:
- **Base URL**: `https://testsites.azsystems.tech`
- **Login**: `POST /api/login`
- **Active Auctions**: `GET /api/auctions/active`
- **Activate Auction**: `PATCH /api/auctions/{id}/activate`
- **Place Bid**: `POST /api/auctions/{id}/bid`