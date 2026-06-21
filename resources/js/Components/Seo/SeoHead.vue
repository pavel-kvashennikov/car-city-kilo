<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const seo = computed(() => page.props.seo ?? {});
const site = computed(() => page.props.seoSite ?? {});

const title = computed(() => seo.value.title || site.value.name || 'Город машин');
const description = computed(() => seo.value.description || '');
const canonical = computed(() => seo.value.canonical || page.props.ziggy?.location || '');
const image = computed(() => seo.value.image || '');
const type = computed(() => seo.value.type || 'website');
const robots = computed(() => seo.value.robots || 'index,follow');
const jsonLd = computed(() => seo.value.jsonLd ?? null);

const jsonLdScript = computed(() => {
    if (!jsonLd.value) return '';
    return JSON.stringify(jsonLd.value);
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta v-if="description" head-key="description" name="description" :content="description" />
        <meta head-key="robots" name="robots" :content="robots" />
        <link v-if="canonical" head-key="canonical" rel="canonical" :href="canonical" />

        <!-- Open Graph -->
        <meta head-key="og:title" property="og:title" :content="title" />
        <meta v-if="description" head-key="og:description" property="og:description" :content="description" />
        <meta head-key="og:type" property="og:type" :content="type" />
        <meta v-if="canonical" head-key="og:url" property="og:url" :content="canonical" />
        <meta v-if="image" head-key="og:image" property="og:image" :content="image" />
        <meta head-key="og:locale" property="og:locale" content="ru_RU" />
        <meta v-if="site.name" head-key="og:site_name" property="og:site_name" :content="site.name" />

        <!-- Twitter Card -->
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="title" />
        <meta v-if="description" head-key="twitter:description" name="twitter:description" :content="description" />
        <meta v-if="image" head-key="twitter:image" name="twitter:image" :content="image" />
        <meta v-if="site.twitter" head-key="twitter:site" name="twitter:site" :content="site.twitter" />

        <!-- Pagination -->
        <link v-if="seo.prev" head-key="prev" rel="prev" :href="seo.prev" />
        <link v-if="seo.next" head-key="next" rel="next" :href="seo.next" />

        <!-- JSON-LD -->
        <component
            v-if="jsonLdScript"
            :is="'script'"
            head-key="json-ld"
            type="application/ld+json"
            v-text="jsonLdScript"
        />
    </Head>
</template>
