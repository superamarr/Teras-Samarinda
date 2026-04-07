# Landing Page Components Documentation

## Struktur Component

```
src/
├── components/
│   ├── ui/
│   │   └── BaseButton.vue          # Reusable button component
│   └── landing/
│       └── HeroSection.vue          # Hero section component
└── views/
    └── LandingView.vue               # Main landing page view
```

---

## Posisi Content & Responsive Settings

### 1. Hero Section Positioning

**Container Position:**

- `.hero-section` →`min-height: 100vh` (fullscreen)
- `.hero-content .row` → `<div class="row min-vh-100 align-items-center">`
  - `min-vh-100` = minimum height 100vh
  - `align-items-center` = vertical center align

**Content Wrapping:**

- `.hero-text-wrapper` → `<div class="hero-text-wrapper">` dengan `padding-top: 80px`
  - Padding mengatur jarak dari atas
  - Nilai bisa diubah: `pt-*` classes (pt-1 s/d pt-5)
  - Atau custom: `padding-top: 100px` untuk lebih besar

**Element Spacing:**

```css
.hero-subtitle {
  margin-bottom: 16px;
} /* mb-3 = 1rem gap ke title */
.hero-title {
  margin-bottom: 24px;
} /* mb-4 = 1.5rem gap ke description */
.hero-description {
  margin-bottom: 48px;
} /* mb-5 = 3rem gap ke buttons */
```

### 2. Cara Menurunkan Posisi Content

#### **Option A: Turunkan Semua Konten (Heading + Deskripsi + Button)**

**Di file `HeroSection.vue`, ubah `.hero-text-wrapper`:**

```css
.hero-text-wrapper {
  padding-top: 120px; /* Ubah dari 80px ke 120px atau lebih */
}
```

**Atau gunakan Bootstrap classes:**

```vue
<!-- Di template -->
<div class="hero-text-wrapper mt-5 pt-5">
  <!-- content -->
</div>
```

#### **Option B: TurunkanHanya Button dari Deskripsi**

**Di file `HeroSection.vue`, ubah `.hero-description`:**

```vue
<p class="hero-description mb-5">  <!-- mb-5 = 3rem -->
```

**Ganti menjadi:**

```vue
<p class="hero-description mb-6">  <!-- custom class untuk lebih besar -->
```

**Atau tambahkan di CSS:**

```vue
<p class="hero-description" style="margin-bottom: 80px;">
```

**Atau tambahkan margin-top pada button group:**

```vue
<div class="d-flex flex-column flex-sm-row gap-3 mt-5">  <!-- mt-5 = 3rem -->
```

#### **Option C: Hilangkan.Center Alignment**

**Jika ingin konten mulai dari atas (bukan tengah):**

**Di `HeroSection.vue`, hapus:**

```vue
<!-- HAPUS align-items-center -->
<div class="row min-vh-100 align-items-center">
```

**Ganti dengan:**

```vue
<div class="row min-vh-100">
```

**Kemudian tambahkan padding besar:**

```vue
<div class="hero-text-wrapper pt-5 mt-5">
```

---

## Responsive Breakpoints

### Mobile (< 576px /sm)

```css
@media (max-width: 576px) {
  .hero-title {
    font-size: clamp(2.5rem, 10vw, 3rem);
  }
  .hero-description {
    font-size: 1rem;
    max-width: 100%;
  }
  .hero-text-wrapper {
    padding-top: 60px;
    text-align: center; /* Center pada mobile */
  }
}
```

**Spacing:**

- Title: `font-size: 2.5rem` - 3rem
- Description: `font-size: 1rem`
- Padding top: `60px`
- Text alignment: center

### Tablet (576px - 992px / sm - lg)

```css
@media (min-width: 576px) and (max-width: 992px) {
  .hero-text-wrapper {
    padding-top: 70px;
  }
  .hero-title {
    font-size: clamp(3rem, 5vw, 4rem);
  }
}
```

**Spacing:**

- Title: `font-size: 3rem` - 4rem
- Padding top: `70px`

### Desktop (> 992px / lg)

```css
@media (min-width: 992px) {
  .hero-text-wrapper {
    padding-top: 80px;
  }
  .hero-title {
    font-size: clamp(3rem, 6vw, 6rem);
  }
}
```

**Spacing:**

- Title: `font-size: 3rem` - 6rem
- Padding top: `80px`

---

## Spacing Classes Reference (Bootstrap)

### Margin

```
m-1: 0.25rem (4px)
m-2: 0.5rem  (8px)
m-3: 1rem    (16px)
m-4: 1.5rem  (24px)
m-5: 3rem    (48px)

mt-1 sampai mt-5  (margin-top)
mb-1 sampai mb-5  (margin-bottom)
my-1 sampai my-5  (margin-top & margin-bottom)
mx-1 sampai mx-5  (margin-left & margin-right)
```

### Padding

```
p-1: 0.25rem (4px)
p-2: 0.5rem  (8px)
p-3: 1rem    (16px)
p-4: 1.5rem  (24px)
p-5: 3rem    (48px)

pt-1 sampai pt-5  (padding-top)
pb-1 sampai pb-5  (padding-bottom)
py-1 sampai py-5  (padding-top & padding-bottom)
px-1 sampai px-5  (padding-left & padding-right)
```

### Gap (Flexbox)

```
gap-1: 0.25rem (4px)
gap-2: 0.5rem  (8px)
gap-3: 1rem    (16px)   ← RECOMMENDED untuk buttons
gap-4: 1.5rem  (24px)
gap-5: 3rem    (48px)
```

---

## Specific Position Adjustments

### Menurunkan Heading dari Atas

```vue
<!-- HeroSection.vue -->
<div class="hero-text-wrapper pt-5 mt-5">
```

### Menurunkan Deskripsi dari Heading

```vue
<h1 class="hero-title mb-5">  <!-- mb-5 = 3rem gap (dari mb-4 = 1.5rem) -->
```

### Menurunkan Button dari Deskripsi

```vue
<p class="hero-description mb-6">  <!-- mb-6 adalah custom, perlu di CSS -->
```

**Atau:**

```vue
<div class="d-flex flex-column flex-sm-row gap-3 mt-5">
```

### Menurunkan Semua Konten Secara Proporsional

```vue
<!-- HeroSection.vue -->
<div class="row min-vh-100">
  <div class="col-lg-8 col-md-10 col-12">
    <div class="hero-text-wrapper pt-5 mt-5">
      <!-- content -->
    </div>
  </div>
</div>
```

---

## BaseButton Component Props

```vue
<BaseButton
  variant="light"           <!-- light, primary, secondary, outline-light, outline-primary -->
  shape="pill"              <!-- default, pill, circle -->
  size="lg"                 <!-- sm, md, lg -->
  href="#events"            <!-- untuk link -->
  :loading="isLoading"     <!-- loading state -->
  :disabled="isDisabled"    <!-- disabled state -->
>
  Button Text
</BaseButton>
```

### Examples

**Pill Button (Text):**

```vue
<BaseButton variant="light" shape="pill" size="lg">
  LIHAT EVENT
</BaseButton>
```

**Circle Button (Icon):**

```vue
<BaseButton variant="outline-light" shape="circle" size="md">
  <svg>...</svg>
</BaseButton>
```

**Button with Click Handler:**

```vue
<BaseButton @click="handleClick" variant="primary">
  Submit
</BaseButton>
```

---

## File Changes Summary

### Created Files:

1. `src/components/ui/BaseButton.vue` - Reusable button component
2. `src/components/landing/HeroSection.vue` - Hero section component

### Modified Files:

1. `src/views/LandingView.vue` - Refactored to use HeroSection component

### Key Changes:

- ✅ Extracted Hero section into dedicated component
- ✅ Created reusable BaseButton component
- ✅ Made responsive untuk mobile/tablet/desktop
- ✅ Organized structure: `components/ui/` and `components/landing/`
- ✅ Added proper spacing with Bootstrap utilities
- ✅ Clean separation of concerns

---

## Next Steps

1. **Add more sections** (About, Events, Gallery, Contact)
2. **Create Navbar component** (src/components/landing/Navbar.vue)
3. **Create Footer component** (src/components/landing/Footer.vue)
4. **Add more UI components** (BaseCard, BaseInput, etc.)
5. **Implement dashboard components** (for CMS admin area)

---

## Quick Reference

**Center alignment:**

```vue
<div class="row justify-content-center align-items-center min-vh-100">
```

**Left alignment (current):**

```vue
<div class="row align-items-center min-vh-100">
```

**Top alignment:**

```vue
<div class="row min-vh-100 pt-5">
```

**Responsive text size:**

```css
font-size: clamp(min-size, preferred-size, max-size);
font-size: clamp(2.5rem, 5vw, 6rem);
```
