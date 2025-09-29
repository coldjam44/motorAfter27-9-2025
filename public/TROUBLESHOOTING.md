# 🔧 Troubleshooting Guide - Auction APIs

## ❌ Common Errors and Solutions

### 1. "The GET method is not supported for route api/login"

**Error Message:**
```json
{
    "message": "The GET method is not supported for route api/login. Supported methods: POST."
}
```

**Cause:** Trying to access login endpoint with GET method instead of POST.

**Solution:** ✅
- Use `POST` method only for login
- Check your Postman request method
- If using browser, don't navigate to login URL directly

**Correct cURL:**
```bash
curl -X POST "https://testsites.azsystems.tech/api/login" \
  -H "Accept: application/json" \
  -F "email=azsystems@ajwaaalsallmah.com" \
  -F "password=Aa123456789" \
  -F "remember_me=1" \
  -k
```

### 2. "The remember me field must be true or false"

**Error Message:**
```json
{
    "message": "The remember me field must be true or false.",
    "errors": {
        "remember_me": ["The remember me field must be true or false."]
    }
}
```

**Cause:** Using string `"true"` instead of boolean or numeric value.

**Solution:** ✅
- Use `1` for true
- Use `0` for false  
- Don't use string `"true"` or `"false"`

### 3. "301 Moved Permanently"

**Error Message:**
```html
<html>
<head><title>301 Moved Permanently</title></head>
<body>
<center><h1>301 Moved Permanently</h1></center>
</body>
</html>
```

**Cause:** Using HTTP instead of HTTPS.

**Solution:** ✅
- Use `https://testsites.azsystems.tech` instead of `http://`
- Add `-k` flag to curl for SSL issues

### 4. "Token expired" or "Invalid token"

**Error Message:**
```json
{
    "status": false,
    "message": "انتهت صلاحية التوكن"
}
```

**Solution:** ✅
- Login again to get new token
- Check token format (should be JWT)
- Ensure token is properly passed in Authorization header

### 5. "Unauthorized" or "التوكن غير موجود"

**Cause:** Missing or malformed Authorization header.

**Solution:** ✅
```bash
# Correct format:
-H "Authorization: Bearer YOUR_JWT_TOKEN"

# NOT:
-H "Authorization: YOUR_JWT_TOKEN"
```

## 🧪 Quick Test Commands

### Test Login:
```bash
curl -X POST "https://testsites.azsystems.tech/api/login" \
  -H "Accept: application/json" \
  -F "email=azsystems@ajwaaalsallmah.com" \
  -F "password=Aa123456789" \
  -F "remember_me=1" \
  -k
```

### Test Token (replace TOKEN):
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
curl -X GET "https://testsites.azsystems.tech/api/user" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -k
```

### Test Active Auctions:
```bash
curl -X GET "https://testsites.azsystems.tech/api/auctions/active" \
  -H "Accept: application/json" \
  -k
```

## 📋 Checklist for API Requests

### For Login:
- ✅ Method: POST
- ✅ URL: https://testsites.azsystems.tech/api/login
- ✅ Body Type: form-data
- ✅ Email: azsystems@ajwaaalsallmah.com
- ✅ Password: Aa123456789
- ✅ remember_me: 1

### For Protected Routes:
- ✅ Method: As specified (POST, GET, PATCH, etc.)
- ✅ URL: https://testsites.azsystems.tech/api/...
- ✅ Header: Authorization: Bearer {token}
- ✅ Header: Accept: application/json
- ✅ Header: Content-Type: application/json (for JSON body)

## 🔍 Debugging Tips

1. **Check HTTP Method:** Ensure you're using the right method (POST for login)
2. **Check URL:** Use HTTPS, not HTTP
3. **Check Headers:** Include Accept and Authorization headers
4. **Check Body Format:** Form-data for login, JSON for other requests
5. **Check Token:** Copy full JWT token including dots
6. **Check Response:** Look for error messages in response body

## 📞 Working Example

Here's a complete working example:

```bash
# Step 1: Login
RESPONSE=$(curl -s -X POST "https://testsites.azsystems.tech/api/login" \
  -H "Accept: application/json" \
  -F "email=azsystems@ajwaaalsallmah.com" \
  -F "password=Aa123456789" \
  -F "remember_me=1" \
  -k)

echo "Login Response: $RESPONSE"

# Step 2: Extract Token (requires jq)
TOKEN=$(echo $RESPONSE | jq -r '.token')
echo "Token: $TOKEN"

# Step 3: Use Token
curl -X GET "https://testsites.azsystems.tech/api/auctions/active" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -k
```