<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    zones: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
    selectedCode: { type: [String, Number], default: null },
})

const emit = defineEmits(['select'])

/* =====================================================================
 * Геометрия участка авторынка (Загородное ш., Оренбург).
 * Локальная система координат — прямоугольник PLOT_W × PLOT_H.
 * Всё рисуется в ней, затем поворачивается на угол THETA в экранные
 * координаты функцией rot(). Подписи остаются горизонтальными.
 * ===================================================================== */
const PLOT_W  = 660   // длинная сторона (условные единицы)
const PLOT_H  = 440   // короткая сторона
const THETA   = 32    // угол поворота участка (градусы)
const CENTER  = { x: 500, y: 398 }   // куда помещается центр в SVG-холсте

// Сетка павильонов
const PAD_L   = 64    // левый проезд/въезд
const PAD_R   = 108   // правая парковка (8 рядов попарно)
const PAD_T   = 58    // верхняя парковка (3 ряда × 25)
const PAD_B   = 92    // нижняя парковка  (4 ряда × 60)
const GAP     = 14    // зазор между павильонами

const grid = {
    x0: PAD_L,
    x1: PLOT_W - PAD_R,     // 552
    y0: PAD_T,              // 58
    y1: PLOT_H - PAD_B,     // 348
}

const BAND_X0 = PAD_L            // начало верхней / нижней парковки по X
const BAND_X1 = PLOT_W - 8       // конец

const carColors = ['#c9cfd6','#8c97a3','#5b6470','#2b3240','#9ab0c6','#b34a4a','#3a5bbf','#4a7c4e']
const zoneColors = ['#2563eb','#0d9488','#d97706','#7c3aed','#db2777','#4f46e5','#059669','#ea580c']

const rad = (THETA * Math.PI) / 180
const ct  = Math.cos(rad)
const st  = Math.sin(rad)

/** Перевод из локальных координат в SVG-пространство */
function rot(lx, ly) {
    const X = lx - PLOT_W / 2
    const Y = ly - PLOT_H / 2
    return [+(CENTER.x + X * ct - Y * st).toFixed(2),
            +(CENTER.y + X * st + Y * ct).toFixed(2)]
}
const poly  = (pts) => pts.map(p => p.join(',')).join(' ')
const rPoly = (lx, ly, w, h) => poly([rot(lx,ly), rot(lx+w,ly), rot(lx+w,ly+h), rot(lx,ly+h)])
const rCenter = (lx, ly, w, h) => rot(lx + w/2, ly + h/2)

/* ------------------------------------------------------------------ *
 * Контур участка                                                       *
 * ------------------------------------------------------------------ */
const plotOutline = computed(() =>
    poly([rot(0,0), rot(PLOT_W,0), rot(PLOT_W,PLOT_H), rot(0,PLOT_H)])
)

/* ================================================================== *
 * ПАВИЛЬОНЫ                                                            *
 *  Ряд 0 : 3 широких (каждый = 2 обычных по ширине)                  *
 *  Ряд 1 : 6 обычных                                                  *
 *  Ряд 2 : 6 обычных                                                  *
 * ================================================================== */
const items = computed(() => {
    const out = []
    ;(props.zones || []).forEach((z, zi) => {
        const color = z.svg_path?.color || zoneColors[zi % zoneColors.length]
        ;(z.locations || []).forEach(loc => out.push({ loc, zone: z, color }))
    })
    if (!out.length) {
        ;(props.locations || []).forEach((loc, i) => out.push({ loc, zone: null, color: zoneColors[i % zoneColors.length] }))
    }
    return out
})

function makePavilion(it, lx, ly, w, h) {
    const loc  = it?.loc
    const pad  = Math.min(7, w * 0.07)
    const u    = Math.min(16, w * 0.18)
    return {
        id:       loc?.id ?? `ep-${Math.round(lx)}-${Math.round(ly)}`,
        code:     loc?.code  ?? null,
        color:    it?.color  ?? '#94a3b8',
        zone:     it?.zone   ?? null,
        company:  loc?.company ?? null,
        status:   loc?.status  ?? 'available',
        occupied: loc?.status === 'occupied' && !!loc?.company,
        reserved: loc?.status === 'reserved',
        empty:    !loc,
        outer:    rPoly(lx, ly, w, h),
        inner:    rPoly(lx+pad, ly+pad, w-2*pad, h-2*pad),
        ridge:    [rot(lx+pad, ly+h/2), rot(lx+w-pad, ly+h/2)],
        unit:     rPoly(lx+w/2-u, ly+h/2-u/2, 2*u, u),
        center:   rCenter(lx, ly, w, h),
        dot:      rot(lx+w-13, ly+13),
    }
}

const pavilions = computed(() => {
    const data    = items.value
    const areaW   = grid.x1 - grid.x0   // 488
    const areaH   = grid.y1 - grid.y0   // 290

    const NORM_COLS = 6
    const normCellW = (areaW - (NORM_COLS - 1) * GAP) / NORM_COLS  // ≈ 69
    const wideCellW = 2 * normCellW + GAP                           // ≈ 152
    const TOTAL_ROWS = 3
    const cellH     = (areaH - (TOTAL_ROWS - 1) * GAP) / TOTAL_ROWS // ≈ 87

    const result = []
    let idx = 0

    // Ряд 0: 3 широких
    for (let c = 0; c < 3; c++) {
        const lx = grid.x0 + c * (wideCellW + GAP)
        result.push(makePavilion(data[idx++], lx, grid.y0, wideCellW, cellH))
    }
    // Ряды 1–2: по 6 обычных
    for (let row = 1; row < TOTAL_ROWS; row++) {
        for (let c = 0; c < NORM_COLS; c++) {
            const lx = grid.x0 + c * (normCellW + GAP)
            const ly = grid.y0 + row * (cellH + GAP)
            result.push(makePavilion(data[idx++], lx, ly, normCellW, cellH))
        }
    }
    return result
})

/* ================================================================== *
 * ПАРКОВОЧНЫЕ МЕСТА                                                    *
 * ================================================================== */

/** Стабильная псевдослучайная заполненность по коду */
const _occ  = (code) => { const h = [...code].reduce((a,c)=>a+c.charCodeAt(0),0); return (h*7+3)%10 < 4 }
const _ccar = (code) => { const h = [...code].reduce((a,c)=>a+c.charCodeAt(0),0); return carColors[(h*13+5)%carColors.length] }

function makeStall(code, lx, ly, w, h) {
    const occupied = _occ(code)
    const mw = w * 0.64, mh = h * 0.64
    const mx = lx + (w - mw) / 2, my = ly + (h - mh) / 2
    return {
        code,
        points:   rPoly(lx+0.6, ly+0.6, w-1.2, h-1.2),
        car:      occupied ? { points: rPoly(mx, my, mw, mh), fill: _ccar(code) } : null,
        occupied,
    }
}

/* ---- ВЕРХНЯЯ парковка: 3 ряда × 25 = 75 ---- */
const topParking = computed(() => {
    const spots = []
    const x0 = BAND_X0, x1 = BAND_X1
    const y0 = 6, y1 = grid.y0          // 6…58  (52 единицы)
    const cols = 25, rows = 3
    const sW = (x1 - x0) / cols
    const sH = (y1 - y0) / rows
    let n = 0
    for (let r = 0; r < rows; r++)
        for (let c = 0; c < cols; c++)
            spots.push(makeStall(`PT-${String(++n).padStart(3,'0')}`, x0 + c*sW, y0 + r*sH, sW, sH))
    return spots
})

/* ---- ПРАВАЯ парковка: 8 рядов попарно × ~24 = ~192 ---- */
/* Ряды идут вдоль длинной оси (по Y), глубина — в X-сторону от сетки. */
const rightParking = computed(() => {
    const spots = []
    const stallDepth = 9       // ширина ряда в X
    const innerGap   = 2       // зазор внутри пары
    const pairGap    = 6       // дорога между парами
    const stallLen   = 12      // длина места вдоль Y

    const y0 = grid.y0, y1 = grid.y1      // 58 … 348
    const count = Math.floor((y1 - y0) / stallLen)   // ~24
    let curX = grid.x1 + 8                            // 560
    let n = 0

    for (let pair = 0; pair < 4; pair++) {
        for (let inPair = 0; inPair < 2; inPair++) {
            for (let s = 0; s < count; s++) {
                const lx = curX
                const ly = y0 + s * stallLen
                spots.push(makeStall(`PR-${String(++n).padStart(3,'0')}`, lx, ly, stallDepth, stallLen))
            }
            curX += stallDepth + (inPair === 0 ? innerGap : 0)
        }
        if (pair < 3) curX += pairGap
    }
    return spots
})

/* ---- НИЖНЯЯ парковка: 4 ряда × 60 = 240 (2 пары с дорогой) ---- */
const bottomParking = computed(() => {
    const spots = []
    const x0 = BAND_X0, x1 = BAND_X1
    const startY  = grid.y1 + 8           // 356
    const stallW  = (x1 - x0) / 60
    const stallH  = 16
    const inGap   = 2
    const pairGap = 8
    let n = 0

    for (let pair = 0; pair < 2; pair++) {
        for (let inPair = 0; inPair < 2; inPair++) {
            const ly = startY
                + pair * (2 * stallH + inGap + pairGap)
                + inPair * (stallH + inGap)
            for (let c = 0; c < 60; c++)
                spots.push(makeStall(`PB-${String(++n).padStart(3,'0')}`, x0 + c*stallW, ly, stallW, stallH))
        }
    }
    return spots
})

const allStalls = computed(() => [...topParking.value, ...rightParking.value, ...bottomParking.value])

/* ================================================================== *
 * Трасса, въезд                                                        *
 * ================================================================== */
const highway = computed(() => {
    const y1 = PLOT_H + 38, y2 = PLOT_H + 68
    return {
        band: poly([rot(-120,y1), rot(PLOT_W+70,y1), rot(PLOT_W+70,y2), rot(-120,y2)]),
        dash: [rot(-120,(y1+y2)/2), rot(PLOT_W+70,(y1+y2)/2)],
    }
})
const entrance = computed(() =>
    poly([rot(PAD_L+20, PLOT_H), rot(PAD_L+90, PLOT_H), rot(PAD_L+90, PLOT_H+38), rot(PAD_L+20, PLOT_H+38)])
)

/* ================================================================== *
 * Pan / zoom                                                           *
 * ================================================================== */
const k = ref(1), tx = ref(0), ty = ref(0)
const dragging = ref(false), moved = ref(false)
const last = ref({ x: 0, y: 0 })

const zoomIn  = () => (k.value = Math.min(k.value * 1.25, 6))
const zoomOut = () => (k.value = Math.max(k.value / 1.25, 0.5))
const reset   = () => { k.value = 1; tx.value = 0; ty.value = 0 }

const onWheel = (e) => { const d = e.deltaY < 0 ? 1.12 : 1/1.12; k.value = Math.min(6, Math.max(0.5, k.value * d)) }
const onDown  = (e) => { dragging.value = true; moved.value = false; last.value = { x: e.clientX, y: e.clientY } }
const onMove  = (e) => {
    if (!dragging.value) return
    const dx = e.clientX - last.value.x, dy = e.clientY - last.value.y
    if (Math.abs(dx) + Math.abs(dy) > 3) moved.value = true
    tx.value += dx; ty.value += dy; last.value = { x: e.clientX, y: e.clientY }
}
const onUp = () => (dragging.value = false)
const gTransform = computed(() => `translate(${tx.value} ${ty.value}) scale(${k.value})`)

/* ================================================================== *
 * Выбор                                                                *
 * ================================================================== */
const isSel = (code) => props.selectedCode != null && String(code) === String(props.selectedCode)

const clickPav = (p) => {
    if (moved.value || p.empty) return
    emit('select', { kind: 'pavilion', location: p, zone: p.zone })
}
const clickStall = (s) => {
    if (moved.value) return
    emit('select', { kind: 'stall', stall: { code: s.code, occupied: s.occupied } })
}

const pavDotFill = (p) => {
    if (p.empty)     return '#e8edf3'
    if (p.occupied)  return '#22c55e'
    if (p.reserved)  return '#f59e0b'
    return '#94a3b8'
}

const totalStalls = computed(() => allStalls.value.length)
</script>

<template>
    <div class="relative overflow-hidden rounded-2xl border border-outline bg-[#cdb39a]" style="height: 640px">
        <!-- Кнопки зума -->
        <div class="absolute top-4 right-4 z-10 flex flex-col gap-1.5">
            <button class="map-btn" @click="zoomIn">+</button>
            <button class="map-btn" @click="zoomOut">−</button>
            <button class="map-btn text-[10px]" @click="reset">⟳</button>
        </div>
        <!-- Подпись -->
        <div class="absolute bottom-3 left-3 z-10 rounded-lg bg-white/85 px-2.5 py-1 text-[11px] text-on-surface-muted backdrop-blur">
            Загородное шоссе · авторынок · {{ totalStalls }} парковочных мест
        </div>

        <svg
            viewBox="0 0 1000 900"
            class="h-full w-full select-none"
            :class="dragging ? 'cursor-grabbing' : 'cursor-grab'"
            @wheel.prevent="onWheel"
            @mousedown="onDown" @mousemove="onMove" @mouseup="onUp" @mouseleave="onUp"
        >
            <defs>
                <radialGradient id="mg-ground" cx="40%" cy="28%" r="92%">
                    <stop offset="0%"   stop-color="#d8c3a6"/>
                    <stop offset="60%"  stop-color="#c9ad8c"/>
                    <stop offset="100%" stop-color="#b8987a"/>
                </radialGradient>
                <linearGradient id="mg-roof" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%"   stop-color="#f4f6f9"/>
                    <stop offset="100%" stop-color="#dde3ea"/>
                </linearGradient>
            </defs>

            <!-- Грунтовый фон -->
            <rect x="0" y="0" width="1000" height="900" fill="url(#mg-ground)"/>

            <g :transform="gTransform">
                <!-- Трасса -->
                <polygon :points="highway.band" fill="#3f4651"/>
                <line
                    :x1="highway.dash[0][0]" :y1="highway.dash[0][1]"
                    :x2="highway.dash[1][0]" :y2="highway.dash[1][1]"
                    stroke="#f5d76e" stroke-width="1.5" stroke-dasharray="10 9" opacity="0.85"
                />
                <!-- Въезд -->
                <polygon :points="entrance" fill="#5a626d"/>

                <!-- Асфальт участка -->
                <polygon :points="plotOutline" fill="#6b7280" stroke="#4b5563" stroke-width="1.5"/>

                <!-- ======= Парковочные места ======= -->
                <g
                    v-for="s in allStalls" :key="s.code"
                    class="stall cursor-pointer"
                    @click.stop="clickStall(s)"
                >
                    <polygon
                        :points="s.points"
                        :fill="isSel(s.code) ? '#3b82f6' : (s.occupied ? '#5c6370' : '#7f8893')"
                        :stroke="isSel(s.code) ? '#1d4ed8' : '#c8d0da'"
                        :stroke-width="isSel(s.code) ? 1.5 : 0.5"
                        class="stall-bg"
                    />
                    <!-- Силуэт машины -->
                    <polygon v-if="s.car" :points="s.car.points" :fill="s.car.fill"/>
                </g>

                <!-- ======= Павильоны ======= -->
                <g
                    v-for="p in pavilions" :key="p.id"
                    :class="p.empty ? '' : 'cursor-pointer'"
                    @click.stop="clickPav(p)"
                >
                    <!-- Крыша -->
                    <polygon
                        :points="p.outer"
                        :fill="p.empty ? '#aeb6c1' : 'url(#mg-roof)'"
                        :stroke="isSel(p.code) ? '#1d4ed8' : '#9aa3af'"
                        :stroke-width="isSel(p.code) ? 3 : 1"
                    />
                    <polygon :points="p.inner"  fill="none" stroke="#c4ccd6" stroke-width="0.8" opacity="0.75"/>
                    <line
                        :x1="p.ridge[0][0]" :y1="p.ridge[0][1]" :x2="p.ridge[1][0]" :y2="p.ridge[1][1]"
                        stroke="#b6bfca" stroke-width="0.8" opacity="0.7"
                    />
                    <polygon :points="p.unit"   fill="#ffffff" stroke="#c4ccd6" stroke-width="0.6" opacity="0.7"/>
                    <!-- Метка зоны -->
                    <rect
                        v-if="!p.empty"
                        :x="p.center[0]-13" :y="p.center[1]-9"
                        width="26" height="18" rx="4" :fill="p.color" opacity="0.92"
                    />
                    <text
                        v-if="!p.empty"
                        :x="p.center[0]" :y="p.center[1]+4"
                        text-anchor="middle" font-size="10" font-weight="700" fill="#fff"
                    >{{ p.code }}</text>
                    <!-- Точка статуса -->
                    <circle
                        v-if="!p.empty"
                        :cx="p.dot[0]" :cy="p.dot[1]" r="4"
                        :fill="pavDotFill(p)" stroke="#fff" stroke-width="1.2"
                    />
                </g>
            </g>
        </svg>
    </div>
</template>

<style scoped>
.map-btn {
    width: 2rem; height: 2rem;
    display: flex; align-items: center; justify-content: center;
    border-radius: 0.5rem;
    background: #fff;
    box-shadow: 0 2px 8px rgb(15 23 42 / 0.15);
    font-size: 1rem; font-weight: 600; color: #334155;
    transition: background 0.15s;
}
.map-btn:hover { background: #eef2f8; }
.stall .stall-bg { transition: fill 0.1s; }
.stall:hover .stall-bg { fill: #a3adb8 !important; }
</style>
