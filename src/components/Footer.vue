<script setup>
import { ref, onMounted } from 'vue'
import { contactService } from '@/api/contact'

const contactData = ref({
  email: 'info@terassamarinda.id',
  phone: '+62 811-5504-747',
  address: 'Jl. Gajah Mada, Samarinda',
  facebook: '#',
  instagram: '#'
})

const loadContact = async () => {
  try {
    const res = await contactService.get()
    if (res.data?.success && res.data?.data) {
      const data = res.data.data
      contactData.value.email = data.email || 'info@terassamarinda.id'
      contactData.value.phone = data.phone || '+62 811-5504-747'
      contactData.value.address = data.address || 'Jl. Gajah Mada, Samarinda'
      contactData.value.facebook = data.facebook || '#'
      contactData.value.instagram = data.instagram || '#'
    }
  } catch (error) {
    console.error('Failed to load contact info', error)
  }
}

onMounted(loadContact)
</script>

<template>
  <footer class="footer-section">
    <div class="container footer-container">
      <div class="row gy-5 justify-content-between gx-lg-5">
        <!-- Column 1: Brand & Social -->
        <div class="col-lg-3 col-md-6">
          <div class="footer-brand mb-4">
            <h3 class="brand-title">Teras Samarinda</h3>
          </div>
          <p class="brand-description mb-4">
            Teras Samarinda adalah ruang publik modern di tepian Sungai Mahakam yang menghadirkan
            pengalaman wisata urban yang nyaman, estetik, dan penuh aktivitas bagi masyarakat dan
            wisatawan.
          </p>
          <div class="social-links-wrapper">
            <h6 class="social-label mb-3">IKUTI KAMI</h6>
            <div class="social-icons d-flex gap-3">
              <a :href="contactData.instagram" class="social-icon" target="_blank">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
              </a>
              <a :href="contactData.facebook" class="social-icon" target="_blank">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path
                    d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"
                  ></path>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- Column 2: Menu Utama -->
        <div class="col-lg-3 col-md-6 col-6">
          <h6 class="footer-heading mb-4">MENU UTAMA</h6>
          <ul class="footer-links list-unstyled">
            <li><a href="#">Beranda</a></li>
            <li><a href="#">Tentang</a></li>
            <li><a href="#">Galeri</a></li>
            <li><a href="#">Event</a></li>
            <li><a href="#">Lokasi</a></li>
            <li><a href="#">Kontak</a></li>
          </ul>
        </div>

        <!-- Column 3: Informasi -->
        <div class="col-lg-3 col-md-6 col-6">
          <h6 class="footer-heading mb-4">INFORMASI</h6>
          <ul class="footer-links list-unstyled">
            <li><a href="#">Fasilitas</a></li>
            <li><a href="#">Aktivitas</a></li>
            <li><a href="#">Event Terbaru</a></li>
            <li><a href="#">Galeri Foto</a></li>
            <li><a href="#">Panduan Kunjungan</a></li>
          </ul>
        </div>

        <!-- Column 4: Kontak -->
        <div class="col-lg-3 col-md-6">
          <h6 class="footer-heading mb-4">KONTAK</h6>
          <ul class="contact-info list-unstyled">
            <li class="d-flex align-items-center gap-3 mb-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                ></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <span>{{ contactData.email }}</span>
            </li>
            <li class="d-flex align-items-center gap-3 mb-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                ></path>
              </svg>
              <span>{{ contactData.phone }}</span>
            </li>
            <li class="d-flex align-items-center gap-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>{{ contactData.address }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Copyright Bar -->
      <div class="footer-bottom mt-5">
        <hr class="footer-divider mb-4" />
        <p class="copyright-text text-center">© 2026 Teras Samarinda. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer-section {
  display: block;
  background-color: #1b1b1b !important;
  color: #ffffff;
  padding: var(--section-pad-y) 0 var(--spacing-lg);
}

@media (min-width: 992px) {
  .footer-section {
    padding-top: var(--section-pad-y-lg);
  }
}

.brand-title {
  font-family: var(--font-family-sans);
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: -0.5px;
}

.brand-description {
  font-family: var(--font-family-sans);
  font-size: var(--type-body);
  line-height: 1.55;
  color: rgba(255, 255, 255, 0.7);
  max-width: var(--prose-max-width);
}

.social-label {
  font-family: var(--font-family-sans);
  font-weight: 700;
  font-size: 0.8rem;
  letter-spacing: 1px;
}

.social-icon {
  color: #ffffff;
  transition: opacity 0.3s ease;
}

.social-icon:hover {
  opacity: 0.7;
}

.footer-heading {
  font-family: var(--font-family-sans);
  font-weight: 700;
  font-size: 0.85rem;
  letter-spacing: 1px;
  color: #ffffff;
}

.footer-links li {
  margin-bottom: var(--spacing-sm);
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-family: var(--font-family-sans);
  font-size: var(--font-size-sm);
  transition: all 0.3s ease;
}

.footer-links a:hover {
  color: #ffffff;
  padding-left: var(--spacing-xs);
}

.contact-info li {
  color: rgba(255, 255, 255, 0.7);
  font-family: var(--font-family-sans);
  font-size: var(--font-size-sm);
}

.footer-divider {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.copyright-text {
  font-family: var(--font-family-sans);
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
  .footer-section {
    padding: var(--spacing-lg) 0 var(--spacing-md);
  }
}

/* Consistent column margins */
.footer-container .row > [class*='col-'] {
  padding-left: var(--spacing-md) !important;
  padding-right: var(--spacing-md) !important;
}

@media (min-width: 992px) {
  .footer-container .row > [class*='col-'] {
    padding-left: var(--spacing-lg) !important;
    padding-right: var(--spacing-lg) !important;
  }
}
</style>
