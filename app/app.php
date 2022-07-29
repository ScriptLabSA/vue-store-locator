<?php

/**
 * Template Name: Vue Store Locator App
 */

get_header();
?>

<section class="app-wrapper">
    <?php
    do_action('vsl_before_app');
    ?>
    <div id="vslApp">
        <div class="container">
            <h1>
                App Content
            </h1>
            <div v-show="loading">Loading....</div>
            <div class="app-filters">
                <div>{{stores.length}}</div><input type="search" name="" id="">
            </div>
            <div v-if="stores.length">
                <div v-for="store in stores" :key="store.id" class="vsl-store-item">
                    <div class="vsl-store-logo">
                        <img :src="store.logo" :alt="store.title">
                    </div>
                    <div class="vsl-store-name sl-store-data">
                        <h3>{{store.title}}</h3>
                    </div>
                    <div class="sl-store-data">{{store.suburb}}</div>
                    <div class="sl-store-data">{{store.city}}</div>
                    <div class="sl-store-data">{{store.province}}</div>
                    <div class="sl-store-data">{{store.phone}}</div>
                    <div class="sl-store-actions">
                        <a class="location">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z"/></svg>
                        </a>
                        <a href="" class="open-store"><span class="open-store-text">View</span> <span class="open-store-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg></span></a>
                    </div>
                </div>
            </div>
            <store-viewer></store-viewer>
        </div>
    </div>
    <?php
    do_action('vsl_after_app');
    ?>
</section>

<?php get_footer();
