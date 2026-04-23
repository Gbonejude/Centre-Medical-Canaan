<template>
  <footer class="footer">
    
    <div class="footer-main">
      <div class="container">
        <div class="row g-4">
          
          <div class="col-lg-4 col-md-6">
            <div class="footer-section">
              <div class="footer-brand">
                <Link class="navbar-logo me-3" :href="route('home.index')">
                  <img :src="LogoCCS" alt="logoCCS" />
                </Link>
                <span>CENTRE MÉDICAL CANAAN</span>
              </div>
              <p class="footer-description">
                Votre partenaire de confiance pour des soins de santé de qualité.
                Nous mettons l'excellence médicale à votre service.
              </p>
            </div>
          </div>

          
          <div class="col-lg-2 col-md-6">
            <div class="footer-section">
              <h5 class="footer-title">Liens Rapides</h5>
              <ul class="footer-links">
                <li><Link :href="route('home.index')">Accueil</Link></li>
                <li><Link :href="route('home.index') + '#faq'">FAQ</Link></li>
                <li><Link :href="route('auth.loginForm')">Espace Personnel</Link></li>
              </ul>
            </div>
          </div>

          
          <div class="col-lg-3 col-md-6">
            <div class="footer-section">
              <h5 class="footer-title">Contactez-nous</h5>
              <div class="contact-info">
                <div class="contact-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Lomé, Togo</span>
                </div>
                <div class="contact-item">
                  <i class="fas fa-phone"></i>
                  <span>+228 90 00 00 00</span>
                </div>
                <div class="contact-item">
                  <i class="fas fa-envelope"></i>
                  <span>contact@centremédicalcanaan.tg</span>
                </div>
              </div>
            </div>
          </div>

          
          <div class="col-lg-3 col-md-6">
            <div class="footer-section">
              <h5 class="footer-title">Suivez-nous</h5>
              <div class="social-links">
                <a href="#" class="social-link">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link">
                  <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link">
                  <i class="fab fa-whatsapp"></i>
                </a>
              </div>

              
              <div class="newsletter">
                <h6 class="newsletter-title">Newsletter</h6>
                <p class="newsletter-text">Recevez nos dernières actualités médicales</p>
                <form class="newsletter-form" @submit.prevent="subscribeNewsletter">
                  <div class="input-group">
                    <input type="email" class="form-control newsletter-input" placeholder="Votre email" v-model="email" />
                    <button class="btn newsletter-btn" type="submit">
                      <i class="fas fa-paper-plane"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="footer-bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="copyright">
              <p>&copy; {{ currentYear }} CENTRE MÉDICAL CANAAN. Tous droits réservés.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="footer-bottom-links">
              <a href="#">Politique de Confidentialité</a>
              <span class="separator">|</span>
              <a href="#">Conditions d'Utilisation</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const LogoCCS = '/assets/img/logo.png'
const email = ref('')
const currentYear = new Date().getFullYear()

const subscribeNewsletter = () => {
  if (email.value) {
    email.value = ''
    alert('Merci pour votre inscription !')
  }
}
</script>

<style scoped>
:root {
    --primary-blue: #3b5998;
    --light-text: #ecf0f1;
    --muted-text: #95a5a6;
}

.footer { background: #1a252f; color: var(--light-text); margin-top: auto; }
.footer-main { padding: 4rem 0 2rem; }
.footer-brand { display: flex; align-items: center; font-size: 1.2rem; font-weight: bold; color: #fff; margin-bottom: 1rem; }
.footer-description { color: var(--muted-text); line-height: 1.6; margin-bottom: 2rem; font-size: 0.9rem; }
.footer-title { color: #fff; font-weight: 600; margin-bottom: 1.5rem; position: relative; padding-bottom: 0.5rem; }
.footer-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 30px; height: 2px; background: #3b5998; }
.footer-links { list-style: none; padding: 0; margin: 0; }
.footer-links li { margin-bottom: 0.75rem; }
.footer-links a { color: var(--muted-text); text-decoration: none; transition: all 0.3s ease; font-size: 0.9rem; }
.footer-links a:hover { color: #3b5998; padding-left: 5px; }
.social-links { display: flex; gap: 1rem; margin-top: 1.5rem; }
.social-link { display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; color: #fff; text-decoration: none; transition: all 0.3s ease; }
.social-link:hover { background: #3b5998; transform: translateY(-3px); }
.contact-info { margin-bottom: 2rem; }
.contact-item { display: flex; align-items: flex-start; margin-bottom: 1rem; color: var(--muted-text); font-size: 0.9rem; }
.contact-item i { color: #3b5998; margin-right: 0.75rem; margin-top: 4px; }
.newsletter { margin-top: 2rem; }
.newsletter-title { color: #fff; font-size: 0.9rem; margin-bottom: 0.5rem; }
.newsletter-text { color: var(--muted-text); font-size: 0.8rem; margin-bottom: 1rem; }
.newsletter-input { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; border-radius: 25px 0 0 25px; }
.newsletter-btn { background: #3b5998; border: none; color: white; border-radius: 0 25px 25px 0; padding: 0.5rem 1rem; }
.footer-bottom { padding: 1.5rem 0; border-top: 1px solid rgba(255, 255, 255, 0.1); }
.copyright p { margin: 0; color: var(--muted-text); font-size: 0.85rem; }
.footer-bottom-links { text-align: right; }
.footer-bottom-links a { color: var(--muted-text); text-decoration: none; font-size: 0.85rem; }
.separator { color: var(--muted-text); margin: 0 0.5rem; }

@media (max-width: 768px) {
    .footer-main { padding: 2rem 0; }
    .footer-bottom-links, .copyright { text-align: center; }
    .footer-bottom-links { margin-top: 1rem; }
    .social-links { justify-content: center; }
}
</style>
