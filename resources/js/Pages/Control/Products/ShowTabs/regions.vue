<script setup>
import {AppAccordion} from '@/Components/Widgets';
import { usePage} from '@inertiajs/vue3';

const props = defineProps({
    product:{}
});
</script>
<template>
    <p class="subheading-regular c-stong-950 mb-3">
        Bölgeler
    </p>
    <p class="paragraph-sm c-sub-600">
       {{usePage().props.product?.regions?.publishing_country_type}}
    </p>
    <div class="flex mt-9">
        <div class="flex flex-col w-full gap-3">

            <div class="w-full" v-for="(value,key) in usePage().props.product?.regions?.countries">
                    <AppAccordion :title="key" description="Tüm ülkeler seçildi">

                        <div class="flex items-center ">
                            <div class="flex-1">
                                <p class="label-medium c-strong-950 !text-start">Ülkeler</p>
                            </div>

                        </div>
                        <hr class="my-2">
                        <div class="grid grid-cols-3 gap-2 ">
                            <div v-for="country in value">
                                <label @click.stop class="flex items-center gap-2 items-center">
                                     {{country.iconKey}}
                                    <span class="paragraph-xs c-strong-950">{{country.label}}</span>
                                </label>
                            </div>
                        </div>
                    </AppAccordion>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
