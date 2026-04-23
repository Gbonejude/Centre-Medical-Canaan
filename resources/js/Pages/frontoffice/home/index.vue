<template>
    <Head>
        <title>Centre Médical Canaan</title>
        <meta name="description" content="Centre Médical Canaan – Prenez rendez-vous en ligne avec nos spécialistes." />
    </Head>

    <div class="cmc-home">

        <!-- ══════════════════ HERO WITH IMAGE ══════════════════ -->
        <section class="hero">
            <div class="hero-bg">
                <img src="/frontend/hospital-hero.png" alt="Canaan Medical Center" />
                <div class="hero-gradient"></div>
            </div>
            <div class="hero-content container">
                <div class="hero-left">
                    <span class="badge-pill"> Centre Médical Canaan</span>
                    <h1>Votre santé est<br><span class="highlight">notre mission</span></h1>
                    <p>Prenez rendez-vous en ligne avec nos spécialistes en quelques clics seulement.</p>
                    <div class="hero-btns">
                        <Link :href="route('appointments.create')" class="btn-appt">
                            <i class="fa fa-calendar-plus"></i> Prendre Rendez-vous
                        </Link>
                        <a href="#services" class="btn-outline-white">
                            Nos Services <i class="fa fa-arrow-down"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick booking card -->
                <div class="quick-card">
                    <div class="quick-card-header">
                        <i class="fa fa-calendar-check"></i>
                        <span>Réservation Rapide</span>
                    </div>
                    <div class="quick-card-body">
                        <div class="quick-field">
                            <label><i class="fa fa-hospital-o"></i> Service</label>
                            <select v-model="quick.service" class="quick-select">
                                <option value="">Choisir un service...</option>
                                <option v-for="s in allServices" :key="s.id" :value="s.name">
                                    {{ s.name }}
                                </option>
                            </select>
                        </div>
                        <div class="quick-field">
                            <label><i class="fa fa-calendar"></i> Date</label>
                            <input type="date" v-model="quick.date" class="quick-input" :min="today" />
                        </div>
                        <Link :href="route('appointments.create')" class="quick-btn">
                            <i class="fa fa-search"></i> Voir les Créneaux Disponibles
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════ SERVICES ══════════════════ -->
        <section class="services-section" id="services">
            <div class="container">
                <div class="section-title">
                    <h2>Nos Services Médicaux</h2>
                    <p>Une prise en charge complète pour tous vos besoins de santé</p>
                </div>
                <div class="services-grid">
                    <div
                        v-for="item in services.data"
                        :key="item.id"
                        class="service-card"
                    >
                        <div class="service-icon">
                            <i :class="item.icon || 'fa fa-stethoscope'"></i>
                        </div>
                        <div class="service-body">
                            <h3>{{ item.name }}</h3>
                            <p>{{ item.description || 'Soins spécialisés assurés par notre équipe médicale certifiée.' }}</p>
                            <Link :href="route('appointments.create')" class="service-link">
                                Prendre rendez-vous <i class="fa fa-arrow-right"></i>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="pagination-wrapper" v-if="services.links && services.links.length > 3">
                    <nav class="pagination-nav">
                        <Link 
                            v-for="(link, k) in services.links" 
                            :key="k"
                            :href="link.url || '#'"
                            class="pagination-link"
                            :class="{ 'active': link.active, 'disabled': !link.url }"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </section>

        <!-- ══════════════════ FAQ SECTION ══════════════════ -->
        <section class="faq-section" id="faq">
            <div class="container">
                <div class="section-title">
                    <h2>Questions Fréquentes</h2>
                    <p>Tout ce que vous devez savoir sur nos services et votre prise en charge</p>
                </div>

                <div class="faq-container">
                    <div 
                        v-for="(item, index) in faqs" 
                        :key="index" 
                        class="faq-item"
                        :class="{ 'active': activeFaq === index }"
                        @click="activeFaq = activeFaq === index ? null : index"
                    >
                        <div class="faq-question">
                            <span>{{ item.question }}</span>
                            <i class="fa" :class="activeFaq === index ? 'fa-minus' : 'fa-plus'"></i>
                        </div>
                        <div class="faq-answer" v-show="activeFaq === index">
                            <p>{{ item.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'

export default {
    name: 'HospitalHome',
    components: { Link, Head },
    props: {
        services: { type: Object, default: () => ({ data: [], links: [] }) },
        allServices: { type: Array, default: () => [] },
    },
    data() {
        return {
            quick: { service: '', date: '' },
            activeFaq: 0,
            faqs: [
                {
                    question: "Comment puis-je prendre rendez-vous en ligne ?",
                    answer: "C'est très simple ! Cliquez sur le bouton 'Prendre rendez-vous', choisissez le service médical souhaité, sélectionnez un médecin et une plage horaire qui vous convient. Vous recevrez une confirmation immédiate."
                },
                {
                    question: "Quels documents dois-je apporter lors de ma consultation ?",
                    answer: "Veuillez vous munir de votre pièce d'identité, de votre carte d'assurance (si applicable) ainsi que de vos derniers examens médicaux ou ordonnances en cours pour faciliter le diagnostic."
                },
                {
                    question: "Puis-je annuler ou reporter un rendez-vous ?",
                    answer: "Oui, vous pouvez gérer vos rendez-vous depuis votre espace personnel. Nous vous prions de bien vouloir nous prévenir au moins 24 heures à l'avance en cas d'annulation."
                },
                {
                    question: "Quels sont les horaires d'ouverture du centre ?",
                    answer: "Le Centre Médical Canaan est à votre service 24 h sur 24, 7 jours sur 7, pour garantir une prise en charge permanente de nos patients."
                },
                {
                    question: "Proposez-vous des consultations en urgence ?",
                    answer: "Oui, nous disposons d'un service d'accueil pour les urgences mineures durant nos heures d'ouverture. Pour toute urgence vitale, veuillez contacter immédiatement les services de secours."
                }
            ]
        }
    },
    computed: {
        today() {
            return new Date().toISOString().split('T')[0]
        },
        displayServices() {
            if (this.services && this.services.length > 0) return this.services
            return [
                { name: 'Médecine Générale',  icon: 'fa fa-stethoscope',   desc: 'Consultations et soins préventifs pour toute la famille.' },
                { name: 'Cardiologie',         icon: 'fa fa-heartbeat',     desc: 'Diagnostic et traitement des maladies cardiovasculaires.' },
                { name: 'Pédiatrie',           icon: 'fa fa-baby',          desc: 'Soins spécialisés pour les enfants et adolescents.' },
                { name: 'Orthopédie',          icon: 'fa fa-bone',          desc: 'Traitement des affections des os, articulations et muscles.' },
                { name: 'Ophtalmologie',       icon: 'fa fa-eye',           desc: 'Examens de la vue et corrections visuelles.' },
                { name: 'Dermatologie',        icon: 'fa fa-hand-sparkles', desc: 'Diagnostic et traitement de toutes les maladies de la peau.' },
                { name: 'Gynécologie',         icon: 'fa fa-venus',         desc: 'Suivi gynécologique, grossesse et santé féminine.' },
                { name: 'Neurologie',          icon: 'fa fa-brain',         desc: 'Diagnostic et traitement des maladies du système nerveux.' },
            ]
        }
    }
}
</script>

<style scoped>
* { box-sizing: border-box; }
.cmc-home { font-family: 'Inter', 'Segoe UI', sans-serif; }

/* ── HERO ── */
.hero { position: relative; min-height: 85vh; display: flex; align-items: center; overflow: hidden; }
.hero-bg { position: absolute; inset: 0; }
.hero-bg img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
.hero-gradient {
    position: absolute; inset: 0;
    background: linear-gradient(105deg, rgba(5,25,65,0.88) 0%, rgba(10,45,110,0.65) 50%, rgba(5,25,65,0.4) 100%);
}
.hero-content {
    position: relative; z-index: 2;
    display: flex; align-items: center; justify-content: space-between;
    gap: 3rem; padding: 5rem 1.5rem; flex-wrap: wrap;
}
.hero-left { flex: 1; min-width: 280px; color: white; }
.badge-pill {
    display: inline-block; background: rgba(255,255,255,0.18);
    color: white; padding: 0.4rem 1.1rem; border-radius: 50px;
    font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;
    backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);
}
.hero-left h1 { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; line-height: 1.15; margin-bottom: 1rem; }
.hero-left h1 .highlight { color: #5db8f7; }
.hero-left p { font-size: 1.1rem; opacity: 0.88; line-height: 1.7; margin-bottom: 2rem; max-width: 460px; }
.hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
.btn-appt {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: #2a7de1; color: white;
    padding: 0.95rem 2rem; border-radius: 12px;
    font-weight: 700; font-size: 0.95rem; text-decoration: none;
    transition: all 0.3s; box-shadow: 0 6px 20px rgba(42,125,225,0.45);
}
.btn-appt:hover { background: #1a6dd1; transform: translateY(-2px); text-decoration: none; color: white; }
.btn-outline-white {
    display: inline-flex; align-items: center; gap: 0.5rem;
    border: 2px solid rgba(255,255,255,0.55); color: white;
    padding: 0.95rem 1.8rem; border-radius: 12px;
    font-weight: 600; font-size: 0.95rem; text-decoration: none;
    transition: all 0.3s;
}
.btn-outline-white:hover { background: rgba(255,255,255,0.12); color: white; text-decoration: none; }

/* Quick card */
.quick-card {
    background: white; border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    width: 320px; overflow: hidden; flex-shrink: 0;
}
.quick-card-header {
    background: linear-gradient(135deg, #051941, #2a7de1);
    color: white; padding: 1.2rem 1.5rem;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 1rem; font-weight: 700;
}
.quick-card-header i { font-size: 1.2rem; }
.quick-card-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.quick-field { display: flex; flex-direction: column; gap: 0.4rem; }
.quick-field label { font-size: 0.82rem; font-weight: 600; color: #374151; }
.quick-field label i { color: #2a7de1; margin-right: 0.3rem; }
.quick-select, .quick-input {
    padding: 0.65rem 0.9rem; border: 2px solid #e5e7eb;
    border-radius: 10px; font-size: 0.9rem; outline: none;
    transition: border-color 0.2s; width: 100%;
}
.quick-select:focus, .quick-input:focus { border-color: #2a7de1; }
.quick-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: linear-gradient(135deg, #051941, #2a7de1);
    color: white; padding: 0.85rem; border-radius: 10px;
    font-weight: 700; font-size: 0.9rem; text-decoration: none;
    transition: all 0.3s; margin-top: 0.5rem;
}
.quick-btn:hover { opacity: 0.9; transform: translateY(-2px); text-decoration: none; color: white; }

/* ── SERVICES ── */
.services-section { padding: 90px 0; background: #f4f7fb; }
.section-title { text-align: center; margin-bottom: 3rem; }
.section-title h2 { font-size: 2.2rem; font-weight: 800; color: #051941; margin-bottom: 0.5rem; }
.section-title p { color: #64748b; font-size: 1.05rem; }
.services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 1.5rem; }
.service-card {
    background: white; border-radius: 18px; padding: 1.8rem;
    display: flex; gap: 1.2rem; align-items: flex-start;
    box-shadow: 0 4px 16px rgba(5,25,65,0.07);
    border: 1px solid #e8eef8; transition: all 0.3s;
}
.service-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(5,25,65,0.13); border-color: #2a7de1; }
.service-icon {
    width: 58px; height: 58px; border-radius: 14px;
    background: linear-gradient(135deg, #051941, #2a7de1);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.3rem; flex-shrink: 0;
}
.service-body h3 { font-size: 1.02rem; font-weight: 700; color: #051941; margin-bottom: 0.4rem; }
.service-body p { font-size: 0.86rem; color: #64748b; line-height: 1.6; margin-bottom: 0.8rem; }
.service-link {
    display: inline-flex; align-items: center; gap: 0.35rem;
    color: #2a7de1; font-weight: 600; font-size: 0.85rem;
    text-decoration: none; transition: gap 0.2s;
}
.service-link:hover { gap: 0.6rem; color: #1a6dd1; }

/* ── PAGINATION ── */
.pagination-wrapper { margin-top: 4rem; display: flex; justify-content: center; }
.pagination-nav { display: flex; gap: 0.5rem; background: white; padding: 0.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.pagination-link {
    display: flex; align-items: center; justify-content: center; min-width: 40px; height: 40px;
    padding: 0 0.8rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600;
    color: #64748b; text-decoration: none; transition: all 0.2s;
}
.pagination-link:hover:not(.disabled):not(.active) { background: #f1f5f9; color: #2a7de1; }
.pagination-link.active { background: #2a7de1; color: white; }
.pagination-link.disabled { opacity: 0.5; cursor: not-allowed; }

/* ── FAQ ── */
.faq-section { padding: 90px 0; background: white; }
.faq-container { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 1rem; }
.faq-item {
    border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;
    transition: all 0.3s; cursor: pointer;
}
.faq-item:hover { border-color: #2a7de1; }
.faq-item.active { border-color: #2a7de1; box-shadow: 0 10px 25px rgba(42,125,225,0.1); }
.faq-question {
    padding: 1.2rem 1.5rem; display: flex; justify-content: space-between; align-items: center;
    font-weight: 700; color: #051941; font-size: 1.05rem;
}
.faq-question i { font-size: 0.9rem; color: #2a7de1; }
.faq-answer { padding: 0 1.5rem 1.5rem; color: #64748b; line-height: 1.6; font-size: 0.95rem; animation: fadeIn 0.3s ease; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 900px) {
    .hero-content { flex-direction: column; text-align: center; }
    .hero-btns { justify-content: center; }
    .quick-card { width: 100%; max-width: 400px; }
    .hero-left p { max-width: 100%; }
}
</style>
