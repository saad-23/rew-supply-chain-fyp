# 🔐 Admin Login Guide

## ✅ Test Users Created

Database میں 3 test users موجود ہیں۔ یہیں سے login کریں:

---

## 👨‍✉️ **Admin User**

**Email**: `admin@restaurant.test`  
**Password**: `admin123`  
**URL**: `http://localhost:8000/login`

**جب login کریں:**
```
Email: admin@restaurant.test
Password: admin123
[Login]
```

**پھر یہاں جائے:**
- `http://localhost:8000/admin/dashboard` → سب restaurants دیکھیں
- `http://localhost:8000/admin/pending` → Pending restaurants کو approve/reject کریں

---

## 🏪 **Owner User**

**Email**: `owner@restaurant.test`  
**Password**: `owner123`  
**URL**: `http://localhost:8000/login`

**جب login کریں:**
```
Email: owner@restaurant.test
Password: owner123
[Login]
```

**پھر یہاں جائے:**
- `http://localhost:8000/owner/dashboard` → اپنے restaurants دیکھیں
- `http://localhost:8000/restaurant/create` → نیا restaurant بنائیں
- `http://localhost:8000/restaurant/1/menus` → Menus manage کریں

---

## 👥 **Customer User**

**Email**: `customer@restaurant.test`  
**Password**: `customer123`  
**URL**: `http://localhost:8000/login`

---

## 📱 **Quick Test Flow**

### Step 1: Admin Login
```
1. http://localhost:8000/login
2. Email: admin@restaurant.test
3. Password: admin123
4. Click Login ✓
5. You'll see → http://localhost:8000/admin/dashboard
```

### Step 2: Check Dashboard
- دیکھیں stat cards (Total Restaurants, Pending, Approved)
- دیکھیں design أگر صحیح ہے (colors, fonts, buttons)

### Step 3: Logout & Login as Owner
```
1. Click Logout (اگر ہے تو)
2. Go to http://localhost:8000/login
3. Email: owner@restaurant.test
4. Password: owner123
5. You'll see → http://localhost:8000/owner/dashboard
```

### Step 4: Create Test Restaurant
```
1. http://localhost:8000/restaurant/create
2. Fill form:
   - Name: "The Grand Restaurant"
   - Category: "Fine Dining"
   - City: "Karachi"
   - Address: "123 Main St"
   - Phone: "+92 300 0000000"
   - Email: "grand@rest.test"
   - Description: "A fine dining experience"
3. Submit
4. Restaurant بن جائے گا (Status: Pending)
```

### Step 5: Admin Approves It
```
1. Logout (owner se)
2. Login as admin
3. Go to http://localhost:8000/admin/pending
4. دیکھیں your restaurant کو
5. Click "Approve" button
6. Restaurant approved ہو جائے گا
```

---

## 🎯 کیا Check کریں ہر Screen پر?

### Login Page (`/login`)
- [ ] Form properly styled ہے
- [ ] Inputs میں focus ring ہے (primary color)
- [ ] Buttons purple ہیں (#702f7e)
- [ ] Error messages (اگر wrong password) سرخ ہیں

### Admin Dashboard (`/admin/dashboard`)
- [ ] 3 Stat cards دکھائی دیں:
  - Total Restaurants (اوپر blue gradient)
  - Pending count (orange gradient)
  - Approved count (green gradient)
- [ ] Material Icons دائیں طرف
- [ ] Hover پر shadow add ہو

### Admin Pending (`/admin/pending`)
- [ ] Restaurant cards grid میں
- [ ] ہر card میں Approve اور Reject buttons ہوں
- [ ] Approve button purple ہو
- [ ] Reject button سرخ ہو

### Owner Dashboard (`/owner/dashboard`)
- [ ] اپنے restaurants cards میں
- [ ] Status badges دیکھیں (Approved = green, Pending = yellow)
- [ ] Badges میں Material Icons ہوں
- [ ] Cards hover effect دیں

### Restaurant Create Form (`/restaurant/create`)
- [ ] Form wrapper card styling
- [ ] تمام inputs `.form-input` class استعمال کریں
- [ ] Error messages danger-600 color میں
- [ ] Submit button purple ہو

---

## 📋 Test کے لیے Database Check
```bash
# Terminal میں یہ چلائیں اگر users دوبارہ دیکھنے ہوں:
php test-users.php
```

---

## 🐛 Trouble?

### Login نہیں ہو رہا?
```
1. Check email spelling exactly: admin@restaurant.test
2. Check password: admin123 (exact)
3. Check database میں users ہیں:
   → php artisan tinker
   → >> User::all()
   → >> exit
```

### Routes نہیں ملاں رہی?
```bash
php artisan route:list
```

### Admin dashboard blank ہے?
```bash
# Check اگر restaurants table میں data ہے:
php artisan tinker
>> Restaurant::count()  # کتنے restaurants ہیں
>> exit
```

---

## ✨ Next: Add Test Data

جب login کریں اور ہر چیز کام کر رہی ہو تو:

1. **Create 2-3 Restaurants** as owner
2. **Approve some** as admin
3. **Add menus and items**
4. **Add booking slots**
5. **Test complete flow**

---

**Ab login try karo! 🚀**
