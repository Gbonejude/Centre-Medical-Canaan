<template>
    <section class="login-section">

        <div class="login-box">
            <h1>CANAAN CARE SERVICES</h1>
            <form>
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" placeholder="E-mail">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" placeholder="password">

                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" placeholder="confirm password">

                </div>
                <button type="submit" class="btn btn-green w-100 mb-3">Connexion</button>
                <p class="text-secondary">you already have account ? <span class="fs-2 fw-bold text-blue">Login</span>
                </p>
            </form>
        </div>
    </section>
</template>

<script setup>

</script>

<style>
.login-box::before {
    content: ''; /* Nécessaire pour générer le pseudo-élément */
    position: absolute;
    background-color: #0056b3;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    top: -100px; /* Ajuste la position pour qu'il soit bien visible */
    left: -100px;
    z-index: -1; /* Met le cercle en arrière-plan */
}
.login-box::after {
    content: ''; /* Nécessaire pour générer le pseudo-élément */
    position: absolute;
    background-color: #28a745;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    top: 274px; /* Ajuste la position pour qu'il soit bien visible */
    right: -150px;
    z-index: -1; /* Met le cercle en arrière-plan */
}

.login-section{
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    width: 100%;
}

.login-box {
    background-color: #fff;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
    /* z-index: 2; */
    width: fit-content;
    position: relative;
    /* overflow: hidden; */
    /* text-align: center; */
}

.login-box h1 {
    margin: 0px 0px 30px 0px;
    font-size: 1.5rem;
    font-weight: 600;
    color: #28a745;
}

.form-control {
    border-radius: 10px;
}

.btn-green {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    font-size: 1rem;
}

.btn-green:hover {
    background-color: #218838;
    color: #fff;
}

.btn-blue {
    background-color: #0056b3;
    border-color: #0056b3;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    font-size: 1rem;
}

.btn-blue:hover {
    background-color: #004095;
    color: #fff;
}

.forgot-password {
    font-size: 0.9rem;
    color: #6c757d;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.divider {
    font-size: 1rem;
    font-weight: bold;
    margin: 10px 0;
    color: #6c757d;
    text-align: center;
}
</style>
