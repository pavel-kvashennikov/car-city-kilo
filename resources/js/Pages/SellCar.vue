<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Car, ArrowRight, CheckCircle2, Shield, Zap, Clock, DollarSign,
    FileText, Users, ChevronRight, Star, Phone, TrendingUp,
    Banknote, RefreshCw, Tag, ClipboardList, Camera, BadgeCheck,
} from 'lucide-vue-next';

const steps = [
    { num: '01', title: 'Оставьте заявку', desc: 'Заполните короткую форму онлайн или позвоните нам — оценка занимает 5 минут.' },
    { num: '02', title: 'Выберите программу', desc: 'Trade-in, выкуп или комиссионная продажа — подбираем лучший вариант для вас.' },
    { num: '03', title: 'Осмотр и диагностика', desc: 'Наш специалист осматривает автомобиль на площадке или у вас. Честно и прозрачно.' },
    { num: '04', title: 'Сделка и оплата', desc: 'Подписываем договор, оформляем документы и переводим деньги в день сделки.' },
];

const programs = [
    {
        icon: RefreshCw,
        title: 'Trade-in',
        subtitle: 'Обменяйте автомобиль',
        desc: 'Сдайте ваш автомобиль с пробегом и выберите другой из нашего каталога. Доплата или зачёт — как вам удобно.',
        features: ['Более 500 авто в наличии', 'Оформление за 1 день', 'Выгода на покупку нового'],
        cta: 'Обменять авто',
        href: '/catalog/cars',
        gradient: 'from-blue-500 to-blue-700',
        bg: 'bg-primary-light',
        tag: 'bg-primary-light text-primary',
        tagText: 'Популярно',
    },
    {
        icon: Banknote,
        title: 'Срочный выкуп',
        subtitle: 'Выкупим ваш автомобиль',
        desc: 'Нужны деньги быстро? Выкупаем любые марки и модели в день обращения — даже с обременением и залогом.',
        features: ['Деньги в тот же день', 'Любые марки и модели', 'С залогом и обременением'],
        cta: 'Продать авто',
        href: '#sell-form',
        gradient: 'from-teal-500 to-teal-700',
        bg: 'bg-tertiary-light',
        tag: 'bg-tertiary-light text-tertiary',
        tagText: 'Срочно',
    },
    {
        icon: Tag,
        title: 'Комиссия',
        subtitle: 'Продайте по рыночной цене',
        desc: 'Мы берём все хлопоты на себя: объявление, переговоры, документы. Вы получаете максимальную цену.',
        features: ['Цену устанавливаете вы', 'Продажа до 21 дня', 'Безопасная сделка'],
        cta: 'Подать заявку',
        href: '#sell-form',
        gradient: 'from-amber-500 to-orange-600',
        bg: 'bg-amber-50',
        tag: 'bg-amber-100 text-amber-700',
        tagText: 'Макс. цена',
    },
];

const advantages = [
    { icon: Clock, title: 'Сделка за 1 день', desc: 'Осмотр, оценка, оформление и оплата — всё в день обращения.' },
    { icon: Shield, title: 'Юридическая чистота', desc: 'Проверка залогов, кредитов и ограничений до подписания договора.' },
    { icon: DollarSign, title: 'Лучшая цена', desc: 'Рыночная оценка по 14 параметрам. Никакого занижения.' },
    { icon: BadgeCheck, title: 'Опытная команда', desc: 'Профессиональные оценщики с многолетним опытом на авторынке.' },
    { icon: Zap, title: 'Без лишних поездок', desc: 'Выезд специалиста к вам или оценка онлайн по фото.' },
    { icon: Users, title: 'Поддержка 24/7', desc: 'Менеджер на связи на каждом этапе сделки.' },
];

const parameters = [
    'Марка и модель', 'Год выпуска', 'Пробег', 'Тип кузова',
    'Тип двигателя', 'Коробка передач', 'Привод', 'Цвет',
    'Количество владельцев', 'История ДТП', 'Состояние кузова', 'Регион продажи',
    'Наличие сервисной книги', 'Комплектация',
];

const documents = [
    {
        icon: FileText,
        title: 'Паспорт',
        desc: 'Ваш паспорт гражданина РФ. Если продаёте по доверенности — нотариально заверенный документ.',
    },
    {
        icon: Car,
        title: 'СТС',
        desc: 'Свидетельство о регистрации ТС. Автомобиль должен быть зарегистрирован на продавца.',
    },
    {
        icon: ClipboardList,
        title: 'ПТС',
        desc: 'Паспорт транспортного средства. При электронном ПТС — выписка из ЭПТС или его номер.',
    },
];

const faqs = [
    {
        q: 'Выкупаете ли вы автомобили в кредите или залоге?',
        a: 'Да, мы принимаем автомобили с обременением. Погасим остаток долга из суммы выкупа прямо в банк.',
    },
    {
        q: 'Сколько стоит оценка?',
        a: 'Оценка автомобиля абсолютно бесплатна. Специалист приедет сам или пригласим на площадку.',
    },
    {
        q: 'Когда я получу деньги?',
        a: 'При срочном выкупе — в день сделки. Безналичный перевод или наличные — как вам удобно.',
    },
    {
        q: 'Какие марки вы принимаете?',
        a: 'Принимаем любые легковые автомобили отечественных и иностранных марок вне зависимости от состояния.',
    },
];
</script>

<template>
    <AppLayout>

        <!-- ══════════════ HERO ══════════════ -->
        <section class="bg-gradient-to-br from-carbon-deep via-carbon to-blue-950 text-white relative overflow-hidden">
            <!-- decorative blobs -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-primary/10 blur-3xl" />
                <div class="absolute bottom-0 -left-20 w-72 h-72 rounded-full bg-tertiary/10 blur-3xl" />
            </div>

            <div class="container-app py-16 lg:py-24 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Copy -->
                    <div>
                        <div class="eyebrow !text-blue-300 mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary-bright animate-pulse" />
                            Выкуп и продажа автомобилей
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight mb-5">
                            Продайте автомобиль<br />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-teal-300">
                                выгодно и быстро
                            </span>
                        </h1>
                        <p class="text-gray-400 text-base leading-relaxed mb-8 max-w-lg">
                            Три удобные программы: Trade-in, срочный выкуп или комиссионная продажа.
                            Честная оценка, сделка в день обращения, деньги сразу.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 mb-8">
                            <a href="#sell-form" class="btn-primary !py-3 !px-6">
                                <Car class="w-4 h-4" />
                                Оценить автомобиль
                                <ArrowRight class="w-4 h-4" />
                            </a>
                            <a href="#programs" class="inline-flex items-center justify-center gap-2 rounded-[var(--radius-button)] border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur hover:bg-white/10 transition-colors">
                                Выбрать программу
                            </a>
                        </div>

                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <div v-for="item in [
                                { icon: Shield, text: 'Юридическая чистота' },
                                { icon: Clock, text: 'Сделка за 1 день' },
                                { icon: DollarSign, text: 'Честная оценка' },
                            ]" :key="item.text" class="flex items-center gap-1.5 text-xs text-gray-400">
                                <component :is="item.icon" class="w-3.5 h-3.5 text-teal-400" />
                                {{ item.text }}
                            </div>
                        </div>
                    </div>

                    <!-- Quick eval card -->
                    <div id="sell-form" class="card !bg-white/5 !border-white/10 backdrop-blur-xl p-6 lg:p-8">
                        <h2 class="text-lg font-bold text-white mb-1">Бесплатная оценка</h2>
                        <p class="text-sm text-gray-400 mb-5">Оставьте контакты — перезвоним в течение 15 минут</p>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Марка и модель</label>
                                <input
                                    type="text"
                                    placeholder="Например, Toyota Camry 2021"
                                    class="w-full rounded-[var(--radius-button)] bg-white/10 border border-white/15 text-white placeholder:text-gray-500 px-4 py-2.5 text-sm outline-none focus:border-primary-bright focus:bg-white/15 transition-colors"
                                />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Пробег, км</label>
                                    <input
                                        type="number"
                                        placeholder="65 000"
                                        class="w-full rounded-[var(--radius-button)] bg-white/10 border border-white/15 text-white placeholder:text-gray-500 px-4 py-2.5 text-sm outline-none focus:border-primary-bright focus:bg-white/15 transition-colors"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Год</label>
                                    <input
                                        type="number"
                                        placeholder="2021"
                                        class="w-full rounded-[var(--radius-button)] bg-white/10 border border-white/15 text-white placeholder:text-gray-500 px-4 py-2.5 text-sm outline-none focus:border-primary-bright focus:bg-white/15 transition-colors"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Ваш телефон</label>
                                <input
                                    type="tel"
                                    placeholder="+7 (___) ___-__-__"
                                    class="w-full rounded-[var(--radius-button)] bg-white/10 border border-white/15 text-white placeholder:text-gray-500 px-4 py-2.5 text-sm outline-none focus:border-primary-bright focus:bg-white/15 transition-colors"
                                />
                            </div>
                            <button class="w-full btn-primary !py-3 !text-sm mt-1">
                                <Phone class="w-4 h-4" />
                                Получить оценку бесплатно
                            </button>
                        </div>
                        <p class="text-center text-xs text-gray-600 mt-3">Нажимая кнопку, вы соглашаетесь с обработкой персональных данных</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ STEPS ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="text-center mb-10">
                    <p class="eyebrow mb-2">Как это работает</p>
                    <h2 class="page-title">4 шага до получения денег</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div
                        v-for="(step, i) in steps"
                        :key="step.num"
                        class="card p-5 relative"
                    >
                        <!-- connector line -->
                        <div v-if="i < steps.length - 1"
                            class="hidden lg:block absolute top-8 left-full w-full h-px bg-gradient-to-r from-outline to-transparent z-10 pointer-events-none"
                            style="width: calc(100% - 2rem); left: calc(100% - 0.5rem)"
                        />
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-3xl font-extrabold text-gradient opacity-40 leading-none">{{ step.num }}</span>
                        </div>
                        <h3 class="font-bold text-sm text-on-surface mb-1.5">{{ step.title }}</h3>
                        <p class="text-xs text-on-surface-muted leading-relaxed">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ PROGRAMS ══════════════ -->
        <section id="programs" class="section">
            <div class="container-app">
                <div class="text-center mb-10">
                    <p class="eyebrow mb-2">Программы продажи</p>
                    <h2 class="page-title">Выберите удобный способ</h2>
                    <p class="text-sm text-on-surface-muted mt-2 max-w-xl mx-auto">
                        Trade-in, срочный выкуп или комиссия — подберём оптимальный вариант под вашу ситуацию
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div
                        v-for="program in programs"
                        :key="program.title"
                        class="card overflow-hidden flex flex-col"
                    >
                        <!-- Gradient header -->
                        <div :class="['bg-gradient-to-br p-6 text-white', program.gradient]">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center">
                                    <component :is="program.icon" class="w-5 h-5 text-white" />
                                </div>
                                <span :class="['text-xs font-bold px-2.5 py-1 rounded-full', program.tag]">
                                    {{ program.tagText }}
                                </span>
                            </div>
                            <h3 class="text-xl font-extrabold mb-1">{{ program.title }}</h3>
                            <p class="text-sm text-white/70">{{ program.subtitle }}</p>
                        </div>

                        <!-- Body -->
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-sm text-on-surface-muted leading-relaxed mb-4">{{ program.desc }}</p>
                            <ul class="space-y-2 mb-5 flex-1">
                                <li
                                    v-for="f in program.features"
                                    :key="f"
                                    class="flex items-center gap-2 text-sm text-on-surface"
                                >
                                    <CheckCircle2 class="w-4 h-4 text-success shrink-0" />
                                    {{ f }}
                                </li>
                            </ul>
                            <a :href="program.href" class="btn-secondary !text-sm !py-2.5 text-center w-full">
                                {{ program.cta }} <ArrowRight class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ ADVANTAGES ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left: headline -->
                    <div>
                        <p class="eyebrow mb-3">Наши преимущества</p>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-on-surface leading-tight mb-4">
                            Почему выбирают<br />
                            <span class="text-gradient">Город машин</span>
                        </h2>
                        <p class="text-on-surface-muted text-sm leading-relaxed mb-6 max-w-md">
                            Мы не посредники. Собственная площадка, проверенные резиденты и прозрачные условия —
                            каждая сделка проходит по строгому регламенту.
                        </p>
                        <a href="#sell-form" class="btn-primary !text-sm">
                            Оценить авто сейчас <ArrowRight class="w-4 h-4" />
                        </a>
                    </div>

                    <!-- Right: grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="adv in advantages"
                            :key="adv.title"
                            class="card p-4 flex items-start gap-3"
                        >
                            <div class="w-9 h-9 rounded-xl bg-primary-light flex items-center justify-center shrink-0">
                                <component :is="adv.icon" class="w-4 h-4 text-primary" />
                            </div>
                            <div>
                                <p class="font-bold text-sm text-on-surface mb-0.5">{{ adv.title }}</p>
                                <p class="text-xs text-on-surface-muted leading-relaxed">{{ adv.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ PARAMETERS ══════════════ -->
        <section class="section">
            <div class="container-app">
                <div class="text-center mb-8">
                    <p class="eyebrow mb-2">Честная оценка</p>
                    <h2 class="page-title">Что влияет на стоимость</h2>
                    <p class="text-sm text-on-surface-muted mt-2 max-w-lg mx-auto">
                        Анализируем 14 ключевых параметров — никаких субъективных суждений, только рыночные данные
                    </p>
                </div>

                <div class="card p-6 lg:p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                        <div
                            v-for="param in parameters"
                            :key="param"
                            class="flex items-center gap-2 bg-surface-muted rounded-xl px-3 py-2.5"
                        >
                            <CheckCircle2 class="w-3.5 h-3.5 text-success shrink-0" />
                            <span class="text-xs font-medium text-on-surface leading-tight">{{ param }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ DOCUMENTS ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="text-center mb-8">
                    <p class="eyebrow mb-2">Для оформления</p>
                    <h2 class="page-title">Необходимые документы</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div
                        v-for="doc in documents"
                        :key="doc.title"
                        class="card p-6 flex items-start gap-4"
                    >
                        <div class="w-11 h-11 rounded-xl bg-primary-light flex items-center justify-center shrink-0">
                            <component :is="doc.icon" class="w-5 h-5 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-on-surface mb-1.5">{{ doc.title }}</h3>
                            <p class="text-xs text-on-surface-muted leading-relaxed">{{ doc.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ FAQ ══════════════ -->
        <section class="section">
            <div class="container-app">
                <div class="text-center mb-8">
                    <p class="eyebrow mb-2">Вопросы и ответы</p>
                    <h2 class="page-title">Часто спрашивают</h2>
                </div>

                <div class="max-w-3xl mx-auto grid grid-cols-1 gap-3">
                    <div
                        v-for="faq in faqs"
                        :key="faq.q"
                        class="card p-5"
                    >
                        <h3 class="font-semibold text-sm text-on-surface mb-2 flex items-start gap-2">
                            <Star class="w-4 h-4 text-primary shrink-0 mt-0.5" />
                            {{ faq.q }}
                        </h3>
                        <p class="text-xs text-on-surface-muted leading-relaxed pl-6">{{ faq.a }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ CTA BANNER ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="card overflow-hidden">
                    <div class="bg-gradient-to-br from-carbon-deep via-carbon to-blue-950 p-8 lg:p-14 text-white text-center relative overflow-hidden">
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] rounded-full bg-primary/10 blur-3xl" />
                        </div>
                        <div class="relative z-10">
                            <p class="eyebrow !text-blue-300 mb-4 justify-center">Начните прямо сейчас</p>
                            <h2 class="text-2xl lg:text-4xl font-extrabold mb-4 leading-tight">
                                Готовы продать автомобиль?
                            </h2>
                            <p class="text-gray-400 text-sm max-w-lg mx-auto mb-8 leading-relaxed">
                                Оставьте заявку — наш менеджер свяжется с вами в течение 15 минут,
                                проведёт бесплатную оценку и предложит лучшие условия.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="#sell-form" class="btn-primary !py-3 !px-8">
                                    <Car class="w-4 h-4" />
                                    Оценить автомобиль
                                </a>
                                <Link href="/catalog/cars" class="inline-flex items-center justify-center gap-2 rounded-[var(--radius-button)] border border-white/20 bg-white/5 px-8 py-3 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                                    Посмотреть каталог
                                    <ArrowRight class="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </AppLayout>
</template>
