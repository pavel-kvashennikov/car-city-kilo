<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Car, Layers, Package, GitBranch, Plus, Trash2, Pencil, Check, X, Star, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    tab:         { type: String, default: 'brands' },
    brands:      { type: Array,  default: () => [] },
    models:      { type: Array,  default: () => [] },
    generations: { type: Array,  default: () => [] },
    categories:  { type: Array,  default: () => [] },
    counts:      { type: Object, default: () => ({}) },
    filters:     { type: Object, default: () => ({}) },
});

const activeTab = ref(props.tab);
const setTab = (t) => {
    activeTab.value = t;
    router.get('/admin/catalog', { tab: t }, { preserveState: true, replace: true });
};

const TABS = [
    { key: 'brands',      label: 'Марки',      icon: Car,        count: 'brands' },
    { key: 'models',      label: 'Модели',     icon: Layers,     count: 'models' },
    { key: 'generations', label: 'Поколения',  icon: GitBranch,  count: 'generations' },
    { key: 'categories',  label: 'Категории запчастей', icon: Package, count: 'categories' },
];

// ── Brands ────────────────────────────────────────────────────────────────────
const brandForm = useForm({ name: '', slug: '', is_popular: false, sort_order: 0 });
const addBrand = () => brandForm.post('/admin/catalog/brands', { onSuccess: () => brandForm.reset() });

const editingBrand = ref(null);
const editBrandForm = useForm({ name: '', is_popular: false, sort_order: 0 });
const startEditBrand = (b) => {
    editingBrand.value = b.id;
    editBrandForm.name       = b.name;
    editBrandForm.is_popular = b.is_popular;
    editBrandForm.sort_order = b.sort_order ?? 0;
};
const saveBrand  = (id) => editBrandForm.put(`/admin/catalog/brands/${id}`, { onSuccess: () => { editingBrand.value = null; } });
const delBrand   = (id) => { if (confirm('Удалить марку?')) router.delete(`/admin/catalog/brands/${id}`); };

// ── Models ─────────────────────────────────────────────────────────────────────
const modelBrandFilter = ref(props.filters.brand_id ?? '');
const filteredModels = computed(() =>
    modelBrandFilter.value
        ? props.models.filter(m => String(m.brand_id) === String(modelBrandFilter.value))
        : props.models
);

const modelForm = useForm({ brand_id: '', name: '', is_popular: false });
const addModel  = () => modelForm.post('/admin/catalog/models', { onSuccess: () => { modelForm.reset(); } });

const editingModel = ref(null);
const editModelForm = useForm({ name: '', is_popular: false });
const startEditModel = (m) => { editingModel.value = m.id; editModelForm.name = m.name; editModelForm.is_popular = m.is_popular; };
const saveModel = (id) => editModelForm.put(`/admin/catalog/models/${id}`, { onSuccess: () => { editingModel.value = null; } });
const delModel  = (id) => { if (confirm('Удалить модель?')) router.delete(`/admin/catalog/models/${id}`); };

// ── Generations ────────────────────────────────────────────────────────────────
const genModelFilter = ref(props.filters.model_id ?? '');
const filteredGens = computed(() =>
    genModelFilter.value
        ? props.generations.filter(g => String(g.model_id) === String(genModelFilter.value))
        : props.generations
);

const genForm = useForm({ model_id: '', name: '', year_from: new Date().getFullYear(), year_to: '', body_type: '' });
const addGen  = () => genForm.post('/admin/catalog/generations', { onSuccess: () => genForm.reset() });
const delGen  = (id) => { if (confirm('Удалить поколение?')) router.delete(`/admin/catalog/generations/${id}`); };

// ── Part Categories ────────────────────────────────────────────────────────────
const catForm = useForm({ name: '', slug: '', parent_id: '', description: '', sort_order: 0, is_active: true });
const addCat  = () => catForm.post('/admin/catalog/categories', { onSuccess: () => catForm.reset() });

const editingCat = ref(null);
const editCatForm = useForm({ name: '', parent_id: '', sort_order: 0, is_active: true });
const startEditCat = (c) => {
    editingCat.value = c.id;
    editCatForm.name       = c.name;
    editCatForm.parent_id  = c.parent_id ?? '';
    editCatForm.sort_order = c.sort_order ?? 0;
    editCatForm.is_active  = c.is_active;
};
const saveCat = (id) => editCatForm.put(`/admin/catalog/categories/${id}`, { onSuccess: () => { editingCat.value = null; } });
const delCat  = (id) => { if (confirm('Удалить категорию?')) router.delete(`/admin/catalog/categories/${id}`); };

const rootCategories = computed(() => props.categories.filter(c => !c.parent_id));
const childCategories = (parentId) => props.categories.filter(c => c.parent_id === parentId);
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="page-title">Каталог и справочники</h1>
                <p class="page-subtitle">Управление марками, моделями, поколениями и категориями запчастей</p>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1 border-b border-outline">
                <button v-for="t in TABS" :key="t.key"
                    @click="setTab(t.key)"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition -mb-px"
                    :class="activeTab === t.key
                        ? 'border-primary text-primary'
                        : 'border-transparent text-on-surface-muted hover:text-on-surface'">
                    <component :is="t.icon" class="w-4 h-4" />
                    {{ t.label }}
                    <span class="text-xs bg-surface-muted px-1.5 py-0.5 rounded-full">{{ counts[t.count] ?? 0 }}</span>
                </button>
            </div>

            <!-- ══ BRANDS ══ -->
            <div v-if="activeTab === 'brands'" class="space-y-4">
                <!-- Add form -->
                <form class="card p-4 flex items-end gap-3 flex-wrap" @submit.prevent="addBrand">
                    <div class="flex-1 min-w-40">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Название марки</label>
                        <input v-model="brandForm.name" required placeholder="Toyota" class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-36">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Slug</label>
                        <input v-model="brandForm.slug" placeholder="toyota" class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Порядок</label>
                        <input v-model.number="brandForm.sort_order" type="number" class="input-field w-full !text-sm" />
                    </div>
                    <label class="flex items-center gap-2 pb-1 cursor-pointer">
                        <input type="checkbox" v-model="brandForm.is_popular" class="w-4 h-4 accent-primary" />
                        <span class="text-sm">Популярная</span>
                    </label>
                    <button type="submit" class="btn-primary !text-sm flex items-center gap-1.5 pb-[7px]" :disabled="brandForm.processing">
                        <Plus class="w-4 h-4" /> Добавить
                    </button>
                </form>

                <!-- List -->
                <div class="card overflow-hidden">
                    <div class="divide-y divide-outline">
                        <div v-for="b in brands" :key="b.id" class="px-4 py-3">
                            <!-- View mode -->
                            <div v-if="editingBrand !== b.id" class="flex items-center gap-3">
                                <span class="text-sm font-medium text-on-surface flex-1">{{ b.name }}</span>
                                <span class="text-xs text-on-surface-muted font-mono">{{ b.slug }}</span>
                                <span v-if="b.is_popular" class="text-amber-500" title="Популярная"><Star class="w-3.5 h-3.5 fill-current" /></span>
                                <span class="text-xs text-on-surface-muted">{{ b.models_count }} мод.</span>
                                <button @click="startEditBrand(b)" class="p-1 hover:bg-surface-muted rounded transition-colors"><Pencil class="w-3.5 h-3.5 text-on-surface-muted" /></button>
                                <button @click="delBrand(b.id)" class="p-1 hover:bg-surface-muted rounded transition-colors"><Trash2 class="w-3.5 h-3.5 text-on-surface-muted hover:text-danger" /></button>
                            </div>
                            <!-- Edit mode -->
                            <div v-else class="flex items-center gap-2 flex-wrap">
                                <input v-model="editBrandForm.name" class="input-field !text-sm flex-1 min-w-32" />
                                <input v-model.number="editBrandForm.sort_order" type="number" class="input-field !text-sm w-20" placeholder="Порядок" />
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" v-model="editBrandForm.is_popular" class="w-3.5 h-3.5 accent-primary" />
                                    <span class="text-xs">Популярная</span>
                                </label>
                                <button @click="saveBrand(b.id)" class="p-1.5 bg-success/10 text-success rounded-lg hover:bg-success/20 transition-colors"><Check class="w-4 h-4" /></button>
                                <button @click="editingBrand = null" class="p-1.5 bg-surface-muted rounded-lg hover:bg-surface-dim transition-colors"><X class="w-4 h-4 text-on-surface-muted" /></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ MODELS ══ -->
            <div v-if="activeTab === 'models'" class="space-y-4">
                <!-- Filter -->
                <div class="flex items-center gap-3">
                    <select v-model="modelBrandFilter" class="input-field !text-sm w-48">
                        <option value="">Все марки ({{ models.length }})</option>
                        <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }} ({{ b.models_count }})</option>
                    </select>
                </div>

                <!-- Add form -->
                <form class="card p-4 flex items-end gap-3 flex-wrap" @submit.prevent="addModel">
                    <div class="w-44">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Марка</label>
                        <select v-model="modelForm.brand_id" required class="input-field w-full !text-sm">
                            <option value="">Выберите марку</option>
                            <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Название модели</label>
                        <input v-model="modelForm.name" required placeholder="Camry" class="input-field w-full !text-sm" />
                    </div>
                    <label class="flex items-center gap-2 pb-1 cursor-pointer">
                        <input type="checkbox" v-model="modelForm.is_popular" class="w-4 h-4 accent-primary" />
                        <span class="text-sm">Популярная</span>
                    </label>
                    <button type="submit" class="btn-primary !text-sm flex items-center gap-1.5 pb-[7px]" :disabled="modelForm.processing">
                        <Plus class="w-4 h-4" /> Добавить
                    </button>
                </form>

                <!-- List -->
                <div class="card overflow-hidden">
                    <div class="divide-y divide-outline">
                        <div v-for="m in filteredModels" :key="m.id" class="px-4 py-3">
                            <div v-if="editingModel !== m.id" class="flex items-center gap-3">
                                <span class="text-xs text-on-surface-muted w-24 shrink-0">{{ m.brand?.name }}</span>
                                <span class="text-sm font-medium text-on-surface flex-1">{{ m.name }}</span>
                                <span v-if="m.is_popular" class="text-amber-500"><Star class="w-3.5 h-3.5 fill-current" /></span>
                                <button @click="startEditModel(m)" class="p-1 hover:bg-surface-muted rounded transition-colors"><Pencil class="w-3.5 h-3.5 text-on-surface-muted" /></button>
                                <button @click="delModel(m.id)" class="p-1 hover:bg-surface-muted rounded transition-colors"><Trash2 class="w-3.5 h-3.5 text-on-surface-muted hover:text-danger" /></button>
                            </div>
                            <div v-else class="flex items-center gap-2">
                                <input v-model="editModelForm.name" class="input-field !text-sm flex-1" />
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" v-model="editModelForm.is_popular" class="w-3.5 h-3.5 accent-primary" />
                                    <span class="text-xs">Популярная</span>
                                </label>
                                <button @click="saveModel(m.id)" class="p-1.5 bg-success/10 text-success rounded-lg"><Check class="w-4 h-4" /></button>
                                <button @click="editingModel = null" class="p-1.5 bg-surface-muted rounded-lg"><X class="w-4 h-4 text-on-surface-muted" /></button>
                            </div>
                        </div>
                        <div v-if="!filteredModels.length" class="px-4 py-8 text-center text-sm text-on-surface-muted">
                            Нет моделей для выбранной марки
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ GENERATIONS ══ -->
            <div v-if="activeTab === 'generations'" class="space-y-4">
                <!-- Filter -->
                <div class="flex items-center gap-3">
                    <select v-model="genModelFilter" class="input-field !text-sm w-64">
                        <option value="">Все модели ({{ generations.length }})</option>
                        <option v-for="m in models" :key="m.id" :value="m.id">{{ m.brand?.name }} {{ m.name }}</option>
                    </select>
                </div>

                <!-- Add form -->
                <form class="card p-4 flex items-end gap-3 flex-wrap" @submit.prevent="addGen">
                    <div class="w-52">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Модель</label>
                        <select v-model="genForm.model_id" required class="input-field w-full !text-sm">
                            <option value="">Выберите модель</option>
                            <optgroup v-for="b in brands" :key="b.id" :label="b.name">
                                <option v-for="m in models.filter(x => x.brand_id === b.id)" :key="m.id" :value="m.id">
                                    {{ m.name }}
                                </option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="flex-1 min-w-32">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Поколение / кузов</label>
                        <input v-model="genForm.name" required placeholder="IX (2021-н.в.)" class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-20">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Год с</label>
                        <input v-model.number="genForm.year_from" type="number" class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-20">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Год по</label>
                        <input v-model.number="genForm.year_to" type="number" placeholder="н.в." class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Кузов</label>
                        <input v-model="genForm.body_type" placeholder="Седан" class="input-field w-full !text-sm" />
                    </div>
                    <button type="submit" class="btn-primary !text-sm flex items-center gap-1.5 pb-[7px]" :disabled="genForm.processing">
                        <Plus class="w-4 h-4" /> Добавить
                    </button>
                </form>

                <!-- List -->
                <div class="card overflow-hidden">
                    <div v-if="!filteredGens.length" class="px-4 py-8 text-center text-sm text-on-surface-muted">
                        Поколений нет — выберите модель или добавьте первое
                    </div>
                    <div class="divide-y divide-outline">
                        <div v-for="g in filteredGens" :key="g.id" class="flex items-center gap-3 px-4 py-3">
                            <span class="text-xs text-on-surface-muted w-40 shrink-0">{{ g.car_model?.brand?.name }} {{ g.car_model?.name }}</span>
                            <span class="text-sm font-medium text-on-surface flex-1">{{ g.name }}</span>
                            <span class="text-xs text-on-surface-muted">{{ g.year_from }}–{{ g.year_to ?? 'н.в.' }}</span>
                            <span v-if="g.body_type" class="text-xs bg-surface-muted px-2 py-0.5 rounded">{{ g.body_type }}</span>
                            <button @click="delGen(g.id)" class="p-1 hover:bg-surface-muted rounded transition-colors ml-auto">
                                <Trash2 class="w-3.5 h-3.5 text-on-surface-muted hover:text-danger" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ CATEGORIES ══ -->
            <div v-if="activeTab === 'categories'" class="space-y-4">
                <!-- Add form -->
                <form class="card p-4 flex items-end gap-3 flex-wrap" @submit.prevent="addCat">
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Название категории</label>
                        <input v-model="catForm.name" required placeholder="Тормозная система" class="input-field w-full !text-sm" />
                    </div>
                    <div class="w-44">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Родительская категория</label>
                        <select v-model="catForm.parent_id" class="input-field w-full !text-sm">
                            <option value="">Корневая</option>
                            <option v-for="c in rootCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div class="w-20">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Порядок</label>
                        <input v-model.number="catForm.sort_order" type="number" class="input-field w-full !text-sm" />
                    </div>
                    <label class="flex items-center gap-2 pb-1 cursor-pointer">
                        <input type="checkbox" v-model="catForm.is_active" class="w-4 h-4 accent-primary" />
                        <span class="text-sm">Активна</span>
                    </label>
                    <button type="submit" class="btn-primary !text-sm flex items-center gap-1.5 pb-[7px]" :disabled="catForm.processing">
                        <Plus class="w-4 h-4" /> Добавить
                    </button>
                </form>

                <!-- Tree list -->
                <div class="card overflow-hidden">
                    <div class="divide-y divide-outline">
                        <template v-for="cat in rootCategories" :key="cat.id">
                            <!-- Root category -->
                            <div class="px-4 py-3 bg-surface-muted/30">
                                <div v-if="editingCat !== cat.id" class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-on-surface flex-1">{{ cat.name }}</span>
                                    <span class="text-xs text-on-surface-muted font-mono">{{ cat.slug }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full" :class="cat.is_active ? 'badge-success' : 'badge-neutral'">
                                        {{ cat.is_active ? 'Активна' : 'Скрыта' }}
                                    </span>
                                    <span class="text-xs text-on-surface-muted">{{ childCategories(cat.id).length }} подкат.</span>
                                    <button @click="startEditCat(cat)" class="p-1 hover:bg-surface-muted rounded"><Pencil class="w-3.5 h-3.5 text-on-surface-muted" /></button>
                                    <button @click="delCat(cat.id)" class="p-1 hover:bg-surface-muted rounded"><Trash2 class="w-3.5 h-3.5 text-on-surface-muted hover:text-danger" /></button>
                                </div>
                                <div v-else class="flex items-center gap-2 flex-wrap">
                                    <input v-model="editCatForm.name" class="input-field !text-sm flex-1 min-w-32" />
                                    <input v-model.number="editCatForm.sort_order" type="number" class="input-field !text-sm w-20" />
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" v-model="editCatForm.is_active" class="w-3.5 h-3.5 accent-primary" />
                                        <span class="text-xs">Активна</span>
                                    </label>
                                    <button @click="saveCat(cat.id)" class="p-1.5 bg-success/10 text-success rounded-lg"><Check class="w-4 h-4" /></button>
                                    <button @click="editingCat = null" class="p-1.5 bg-surface-muted rounded-lg"><X class="w-4 h-4 text-on-surface-muted" /></button>
                                </div>
                            </div>

                            <!-- Child categories -->
                            <div v-for="child in childCategories(cat.id)" :key="child.id"
                                class="px-4 py-2.5 border-l-2 border-primary/20 ml-6">
                                <div v-if="editingCat !== child.id" class="flex items-center gap-3">
                                    <ChevronRight class="w-3.5 h-3.5 text-on-surface-muted shrink-0" />
                                    <span class="text-sm text-on-surface flex-1">{{ child.name }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full" :class="child.is_active ? 'badge-success' : 'badge-neutral'">
                                        {{ child.is_active ? 'Активна' : 'Скрыта' }}
                                    </span>
                                    <button @click="startEditCat(child)" class="p-1 hover:bg-surface-muted rounded"><Pencil class="w-3.5 h-3.5 text-on-surface-muted" /></button>
                                    <button @click="delCat(child.id)" class="p-1 hover:bg-surface-muted rounded"><Trash2 class="w-3.5 h-3.5 text-on-surface-muted hover:text-danger" /></button>
                                </div>
                                <div v-else class="flex items-center gap-2 flex-wrap">
                                    <input v-model="editCatForm.name" class="input-field !text-sm flex-1 min-w-32" />
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" v-model="editCatForm.is_active" class="w-3.5 h-3.5 accent-primary" />
                                        <span class="text-xs">Активна</span>
                                    </label>
                                    <button @click="saveCat(child.id)" class="p-1.5 bg-success/10 text-success rounded-lg"><Check class="w-4 h-4" /></button>
                                    <button @click="editingCat = null" class="p-1.5 bg-surface-muted rounded-lg"><X class="w-4 h-4 text-on-surface-muted" /></button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

