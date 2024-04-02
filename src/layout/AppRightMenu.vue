<script setup>
import { useLayout } from '@/layout/composables/layout';

const { layoutState } = useLayout();
</script>
<template>
    <Sidebar v-model:visible="layoutState.rightMenuActive.value" position="right" class="layout-profile-sidebar w-full sm:w-25rem" :pt="{
        closeButton: 'ml-auto'
    }">
        <div class="rightpanel-section">
            <div class="flex align-items-center justify-content-between mb-4">
                <h6 class="m-0 underline">Pomoć i podrška</h6>
            </div>
            <ul class="list-none p-0 m-0 overflow-auto" style="max-height: 330px">
                <li class="p-3 border-round-lg surface-0 flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 mb-2 text-color">Email podrška</h6>
                        <span class="text-400 line-height-3 block font-medium">help@iwashpro.eu</span>
                    </div>
                </li>
                <li class="p-3 border-round-lg surface-0 flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 mb-2 text-color">Telefonska podrška</h6>
                        <span class="text-400 line-height-3 block font-medium"> 00 00 0000 000 </span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="rightpanel-section mt-4">
            <div class="flex align-items-center justify-content-between mb-4">
                <h6 class="m-0 underline">Popis ažuriranja</h6>
            </div>
            <ul class="list-none p-0 m-0 overflow-auto" style="max-height: 330px">
                <li class="p-3 border-round-lg surface-0 flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 mb-2 text-color">31.3.2024. - Update v0.0.2</h6>
                        <span class="text-400 line-height-3 block font-medium">-Public pages</span>
                        <span class="text-400 line-height-3 block font-medium">-Product pages</span>
                    </div>
                </li>
                <li class="p-3 border-round-lg surface-0 flex align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 mb-2 text-color">30.3.2024. - Update v0.0.1</h6>
                        <span class="text-400 line-height-3 block font-medium">-Public pages</span>
                        <span class="text-400 line-height-3 block font-medium">-Product pages</span>
                    </div>
                </li>
            </ul>
        </div>
    </Sidebar>
</template>
