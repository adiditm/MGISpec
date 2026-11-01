<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            open: false,
            q: '',
            group: '',
            icons: window.MGI_ICONS || [],
            groups: window.MGI_ICON_GROUPS || [],
            get current() { return this.icons.find((i) => i.class === this.state) },
            get list() {
                const words = this.q.toLowerCase().trim().split(/\s+/).filter(Boolean)
                return this.icons.filter((i) => (!this.group || i.group === this.group) && words.every((w) => i.search.includes(w)))
            },
            pick(cls) { this.state = cls; this.open = false },
        }"
    >
        <div class="mgi-ip-bar">
            <span class="mgi-ip-tile" :class="{ 'is-empty': !state }"><i :class="state || 'fa-solid fa-plus'"></i></span>
            <div class="mgi-ip-name">
                <span x-text="state ? (current ? current.label : 'Ikon khusus') : 'Belum ada ikon dipilih'"></span>
                <small x-show="state" x-text="state"></small>
            </div>
            <button type="button" class="mgi-ip-btn" x-on:click="open = ! open" x-text="open ? 'Tutup' : (state ? 'Ganti ikon' : 'Pilih ikon')"></button>
        </div>

        <div class="mgi-ip-panel" x-show="open" x-cloak>
            <input type="search" class="mgi-ip-search" x-model="q" placeholder="Cari ikon, misalnya: mata, perisai, cahaya, lapisan" x-on:keydown.enter.prevent>

            <div class="mgi-ip-chips">
                <button type="button" class="mgi-ip-chip" :class="{ 'is-on': ! group }" x-on:click="group = ''">Semua</button>
                <template x-for="g in groups" :key="g">
                    <button type="button" class="mgi-ip-chip" :class="{ 'is-on': group === g }" x-on:click="group = g" x-text="g"></button>
                </template>
            </div>

            <div class="mgi-ip-grid">
                <template x-for="icon in list" :key="icon.class">
                    <button type="button" class="mgi-ip-cell" :class="{ 'is-on': state === icon.class }" :title="icon.label" x-on:click="pick(icon.class)">
                        <span class="mgi-ip-tile"><i :class="icon.class"></i></span>
                        <span class="l" x-text="icon.label"></span>
                    </button>
                </template>
            </div>

            <p class="mgi-ip-meta" x-show="list.length === 0">Tidak ada ikon yang cocok. Coba kata lain, misalnya: mata, perisai, cahaya.</p>
            <p class="mgi-ip-meta" x-show="list.length > 0" x-text="list.length + ' ikon'"></p>

            <details class="mgi-ip-manual">
                <summary>Punya ikon lain dari Font Awesome? Ketik kelasnya</summary>
                <input type="text" x-model.lazy="state" placeholder="fa-solid fa-atom">
            </details>
        </div>
    </div>
</x-dynamic-component>
