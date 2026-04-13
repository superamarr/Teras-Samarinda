import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { MotionPlugin } from '@vueuse/motion'
import VueApexCharts from 'vue3-apexcharts'
import 'bootstrap/dist/css/bootstrap.min.css'
import '@/assets/styles/design-tokens.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import App from './App.vue'
import router from './router'
import entranceDirective from './directives/entrance'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(MotionPlugin)
app.use(VueApexCharts)

// Register custom directives
app.directive('entrance', entranceDirective)

app.mount('#app')
