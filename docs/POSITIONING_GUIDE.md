# Visual Guide: Content Positioning

## Current Structure

```
┌─────────────────────────────────────────────────┐
│  Hero Section (min-height: 100vh)               │
│  ┌───────────────────────────────────────────┐│
│  │  Overlay (dark gradient)                  ││
│  │  ┌───────────────────────────────────────┐││
│  │  │  Hero Content (.container)            │││
│  │  │  ┌───────────────────────────────────┐│││
│  │  │  │  Row (min-vh-100 align-center)    ││││
│  │  │  │  ┌─────────────────────────────┐ ││││
│  │  │  │  │  col-lg-8 col-md-10         │ ││││
│  │  │  │  │  ┌─────────────────────────┐ │ ││││
│  │  │  │  │  │  hero-text-wrapper      │ │ ││││
│  │  │  │  │  │  padding-top: 80px     │ │ ││││
│  │  │  │  │  │                         │ │ ││││
│  │  │  │  │  │  [Subtitle] mb-3 (1rem) │ │ ││││
│  │  │  │  │  │  "Jelajahi Ikon Baru"   │ │ ││││
│  │  │  │  │  │         ↓               │ │ ││││
│  │  │  │  │  │  [Title] mb-4 (1.5rem) │ │ ││││
│  │  │  │  │  │  "Kota Samarinda"       │ │ ││││
│  │  │  │  │  │         ↓               │ │ ││││
│  │  │  │  │  │  [Description] mb-5     │ │ ││││
│  │  │  │  │  │  (3rem gap)            │ │ ││││
│  │  │  │  │  │         ↓               │ │ ││││
│  │  │  │  │  │  [Button Group]        │ │ ││││
│  │  │  │  │  │  [LIHAT EVENT] [→]     │ │ ││││
│  │  │  │  │  │                         │ │ ││││
│  │  │  │  │  └─────────────────────────┘ │ ││││
│  │  │  │  └─────────────────────────────┘ │ │││
│  │  │  └───────────────────────────────────┘││
│  │  └───────────────────────────────────────┘│
│  └───────────────────────────────────────────┘
└─────────────────────────────────────────────────┘
```

## Spacing Values

```
Padding/Margin Values (Bootstrap):
- 0: 0
- 1: 0.25rem (4px)
- 2: 0.5rem  (8px)
- 3: 1rem    (16px)
- 4: 1.5rem  (24px)
- 5: 3rem    (48px)

Custom (add in CSS):
- 6: 4rem    (64px)
- 7: 5rem    (80px)
- 8: 6rem    (96px)
```

---

## How to Adjust Positions

### 1. Turunkan Semua Konten (Heading + Deskripsi + Button)

**Current:**

```css
.hero-text-wrapper {
  padding-top: 80px;
}
```

**Option A - Naikkan 20px:**

```css
.hero-text-wrapper {
  padding-top: 100px; /* dari 80px ke 100px */
}
```

**Option B - Naikkan Signifikan:**

```css
.hero-text-wrapper {
  padding-top: 150px; /* konten lebih ke bawah */
}
```

**Option C - Gunakan Bootstrap Class:**

```vue
<div class="hero-text-wrapper pt-5 mt-5">
  <!-- pt-5 = padding-top: 3rem -->
  <!-- mt-5 = margin-top: 3rem -->
</div>
```

---

### 2. Turunkan Hanya Heading dari Atas

**Current:**

```css
.hero-subtitle {
  margin-bottom: 16px;
} /* mb-3 */
.hero-title {
  margin-bottom: 24px;
} /* mb-4 */
```

**Option A - Perbesar Gap Antara Subtitle dan Title:**

```vue
<p class="hero-subtitle mb-5">  <!-- dari mb-3 ke mb-5 -->
```

**Option B - Custom Gap:**

```vue
<p class="hero-subtitle" style="margin-bottom: 40px;">
```

---

### 3. Turunkan Hanya Button dari Deskripsi

**Current:**

```vue
<p class="hero-description mb-5">  <!-- mb-5 = 3rem -->
```

**Option A - Perbesar Gap:**

```vue
<p class="hero-description mb-7">  <!-- custom, perlu CSS -->
```

**Tambah di CSS:**

```css
.mb-7 {
  margin-bottom: 5rem !important;
} /* 80px */
.mb-8 {
  margin-bottom: 6rem !important;
} /* 96px */
```

**Option B - Tambah Margin Top pada Button Group:**

```vue
<div class="d-flex flex-column flex-sm-row gap-3 mt-5">
  <!-- mt-5 = margin-top: 3rem -->
</div>
```

**Option C - Gabungkan Keduanya:**

```vue
<p class="hero-description mb-5">  <!-- deskripsi ke button: 3rem -->
...
<div class="d-flex flex-column flex-sm-row gap-3 mt-4">  <!-- button tambahan: 1.5rem -->
  <!-- Total gap: 3rem + 1.5rem = 4.5rem -->
</div>
```

---

### 4. Hilangkan Center Alignment (Konten dari Atas)

**Current:**

```vue
<div class="row min-vh-100 align-items-center">
```

**Change to:**

```vue
<div class="row min-vh-100">
  <!-- HAPUS align-items-center -->
```

**Kemudian tambahkan padding besar:**

```vue
<div class="hero-text-wrapper pt-5 mt-5">
  <!-- pt-5 = padding-top: 3rem -->
  <!-- mt-5 = margin-top: 3rem -->
</div>
```

---

## Visual Comparison

### Current (Centered)

```
┌─────────────────────┐
│                     │
│                     │
│   [Padding Top]     │  ← 80px
│   [Subtitle]        │
│        ↓            │
│   [Title]           │  ← mb-4 (24px)
│        ↓            │
│   [Description]     │  ← mb-5 (48px)
│        ↓            │
│   [Buttons]         │
│                     │
│                     │
└─────────────────────┘
   Center Vertically
```

### Lowered Content (pt-5 mt-5)

```
┌─────────────────────┐
│                     │
│[Padding + Margin]    │  ← 80px + 48px + 48px = 176px
│                     │
│   [Subtitle]        │
│        ↓            │
│   [Title]           │
│        ↓            │
│   [Description]     │
│        ↓            │
│   [Buttons]         │
│                     │
│                     │
│                     │
└─────────────────────┘
   Content Lowered
```

### Top-Aligned (NoCenter)

```
┌─────────────────────┐
│[Padding + Margin]    │  ← Top padding only
│                     │
│   [Subtitle]        │
│        ↓            │
│   [Title]           │
│        ↓            │
│   [Description]     │
│        ↓            │
│   [Buttons]         │
│                     │
│                     │
│                     │
│                     │
│                     │
└─────────────────────┘
   TopAligned
```

---

## Quick Reference

### Lower Everything by X pixels:

```css
.hero-text-wrapper {
  padding-top: 120px; /* ganti nilai sesuai kebutuhan */
}
```

### Lower Only Buttons:

```vue
<div class="d-flex flex-column flex-sm-row gap-3 mt-5">
  <!-- mt-5 = 48px margin-top -->
</div>
```

### Center Horizontally (Add):

```vue
<div class="col-lg-8 col-md-10 col-12 text-center">
  <!-- text-center untuk center text -->
</div>
```

### Responsive Padding:

```css
/* Mobile */
@media (max-width: 576px) {
  .hero-text-wrapper {
    padding-top: 60px;
  }
}

/* Tablet */
@media (min-width: 576px) and (max-width: 992px) {
  .hero-text-wrapper {
    padding-top: 70px;
  }
}

/* Desktop */
@media (min-width: 992px) {
  .hero-text-wrapper {
    padding-top: 80px;
  }
}
```

---

## Example Implementations

### Example 1: Lower Content by 40px

```vue
<!-- HeroSection.vue -->
<div class="hero-text-wrapper" style="padding-top: 120px;">
  <!-- konten -->
</div>
```

### Example 2: Increase Button Gap Significantly

```vue
<!-- HeroSection.vue -->
<p class="hero-description mb-7">  <!-- custom class -->
  ...
</p>

<div class="d-flex flex-column flex-sm-row gap-3 mt-5">
  <!-- buttons -->
</div>
```

```css
/* HeroSection.vue <style> */
.mb-7 {
  margin-bottom: 5rem !important;
}
```

### Example 3: Top-Aligned with Large Padding

```vue
<!-- HeroSection.vue -->
<div class="row min-vh-100">
  <!-- hapus align-items-center -->
  <div class="col-lg-8 col-md-10 col-12">
    <div class="hero-text-wrapper pt-5 mt-5">
      <!-- konten -->
    </div>
  </div>
</div>
```

---

## Recommended Spacing

### Standard (Current):

```
- Padding top: 80px
- Subtitle ke Title: 16px (mb-3)
- Title ke Description: 24px (mb-4)
- Description ke Buttons: 48px (mb-5)
```

### Tight (More Compact):

```
- Padding top: 60px
- Subtitle ke Title: 8px (mb-2)
- Title ke Description: 16px (mb-3)
- Description ke Buttons: 24px (mb-4)
```

### Loose (More Breathing Room):

```
- Padding top: 120px
- Subtitle ke Title: 24px (mb-4)
- Title ke Description: 48px (mb-5)
- Description ke Buttons: 80px (custom mb-7)
```

---

## Debug Tips

### Add Borders to See Spacing:

```vue
<div class="hero-text-wrapper" style="border: 2px solid red;">
  <p class="hero-subtitle mb-3" style="border: 1px solid blue;">...</p>
  <h1 class="hero-title mb-4" style="border: 1px solid green;">...</h1>
  <p class="hero-description mb-5" style="border: 1px solid purple;">...</p>
  <div class="d-flex gap-3" style="border: 1px solid orange;">...</div>
</div>
```

### Use Browser DevTools:

1. Right-click on element
2. Inspect
3. Check Computed styles
4. Modify padding/margin in real-time

---

## Final Notes

-**Padding top** mengatur jarak konten dari atas viewpor

- **Margin** mengatur jarak antar elemen (spacing between elements)
- **mb-\*** = margin-bottom (jarak ke bawah)
- **mt-\*** = margin-top (jarak dari atas)
- **pt-\*** = padding-top
- **Semakin besar angka (1-5), semakin besar jaraknya**
- **Custom values bisa ditambah dengan CSS (.mb-6, .mb-7, dll)**
