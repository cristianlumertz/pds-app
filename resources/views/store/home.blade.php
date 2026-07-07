@extends('layouts.app')

@section('content')
    <style>
        .product-carousel-track {
            scrollbar-width: none;
        }

        .product-carousel-track::-webkit-scrollbar {
            display: none;
        }

        .product-carousel-card {
            flex-basis: 84%;
        }

        @media (min-width: 640px) {
            .product-carousel-card {
                flex-basis: calc((100% - 1rem) / 2);
            }
        }

        @media (min-width: 768px) {
            .product-carousel-card {
                flex-basis: calc((100% - 2rem) / 3);
            }
        }

        @media (min-width: 1024px) {
            .product-carousel-card {
                flex-basis: calc((100% - 4rem) / 5);
            }
        }
    </style>

    <section class="overflow-hidden rounded-lg bg-[#1A3A6B]">
        <div class="grid min-h-[360px] gap-8 px-6 py-8 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:px-12 lg:py-12">
            <div class="relative z-10">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold text-white">
                    Capão da Canoa / RS
                </span>

                <h1 class="mt-5 max-w-2xl text-4xl font-black leading-tight text-white lg:text-5xl">
                    Tudo para sua obra em um só lugar
                </h1>

                <p class="mt-4 max-w-xl text-base leading-7 text-white/85">
                    Materiais de construção, ferramentas, tintas e acabamentos com preço justo.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a
                        href="{{ route('store.products') }}"
                        class="rounded bg-[#D42B2B] px-6 py-3 text-sm font-black uppercase text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                    >
                        Conferir ofertas
                    </a>
                    <a
                        href="{{ route('categories.index') }}"
                        class="rounded border border-white/60 px-6 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-[#1A3A6B] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                    >
                        Ver categorias
                    </a>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-2">
                @forelse ($novidades->take(4) as $heroProduct)
                    @php
                        $heroImage = $heroProduct->primaryImageUrl();
                    @endphp

                    <a
                        href="{{ route('store.products.show', $heroProduct->slug) }}"
                        class="group flex min-h-40 flex-col justify-between rounded border border-white/20 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <div class="flex h-24 items-center justify-center">
                            @if ($heroImage)
                                <img src="{{ $heroImage }}" alt="{{ $heroProduct->name }}" class="h-full w-full object-contain">
                            @else
                                <div class="h-full w-full rounded bg-[#F1EFE8]"></div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <p class="line-clamp-2 text-sm font-bold text-[#1A1A1A] group-hover:text-[#1A3A6B]">{{ $heroProduct->name }}</p>
                            <p class="mt-1 text-lg font-black text-[#D42B2B]">R$ {{ number_format((float) $heroProduct->price, 2, ',', '.') }}</p>
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-white/20 bg-white/10 p-6 text-sm font-semibold text-white sm:col-span-3 lg:col-span-2">
                        Produtos em destaque serão exibidos aqui.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @include('store.home._product-carousel', [
        'title' => 'Novidades',
        'products' => $novidades,
        'viewAllUrl' => route('store.products'),
    ])

    @include('store.home._product-carousel', [
        'title' => 'Ofertas em destaque',
        'products' => $ofertas,
        'viewAllUrl' => route('store.products'),
    ])

    @foreach ($categorySections as $section)
        @include('store.home._product-carousel', [
            'title' => $section['title'],
            'products' => $section['products'],
            'viewAllUrl' => route('store.products', ['category' => $section['slug']]),
        ])
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-product-carousel]').forEach((carousel) => {
                const track = carousel.querySelector('[data-carousel-track]');
                const previousButton = carousel.querySelector('[data-carousel-prev]');
                const nextButton = carousel.querySelector('[data-carousel-next]');
                const dotsWrapper = carousel.querySelector('[data-carousel-dots]');

                if (! track) {
                    return;
                }

                const visibleCards = () => {
                    const firstCard = track.querySelector('.product-carousel-card');

                    if (! firstCard) {
                        return 1;
                    }

                    const cardWidth = firstCard.getBoundingClientRect().width;
                    return Math.max(1, Math.round(track.clientWidth / cardWidth));
                };

                const scrollStep = () => {
                    const firstCard = track.querySelector('.product-carousel-card');
                    const gap = parseFloat(getComputedStyle(track).columnGap || '16');

                    return firstCard ? (firstCard.getBoundingClientRect().width + gap) * visibleCards() : track.clientWidth;
                };

                const pageCount = () => Math.max(1, Math.ceil(track.children.length / visibleCards()));

                const currentPage = () => {
                    const step = scrollStep();
                    return step > 0 ? Math.round(track.scrollLeft / step) : 0;
                };

                const updateDots = () => {
                    if (! dotsWrapper) {
                        return;
                    }

                    const pages = pageCount();
                    dotsWrapper.innerHTML = '';

                    if (pages <= 1) {
                        return;
                    }

                    for (let index = 0; index < pages; index++) {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = 'h-2.5 w-2.5 rounded-full bg-[#C5D4EC] transition';
                        dot.setAttribute('aria-label', `Ir para página ${index + 1}`);
                        dot.addEventListener('click', () => {
                            track.scrollTo({ left: scrollStep() * index, behavior: 'smooth' });
                        });

                        dotsWrapper.appendChild(dot);
                    }

                    markActiveDot();
                };

                const markActiveDot = () => {
                    if (! dotsWrapper) {
                        return;
                    }

                    const activeIndex = currentPage();
                    dotsWrapper.querySelectorAll('button').forEach((dot, index) => {
                        dot.classList.toggle('bg-[#1A3A6B]', index === activeIndex);
                        dot.classList.toggle('bg-[#C5D4EC]', index !== activeIndex);
                    });
                };

                previousButton?.addEventListener('click', () => {
                    track.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
                });

                nextButton?.addEventListener('click', () => {
                    track.scrollBy({ left: scrollStep(), behavior: 'smooth' });
                });

                track.addEventListener('scroll', () => window.requestAnimationFrame(markActiveDot));
                window.addEventListener('resize', updateDots);

                updateDots();
            });
        });
    </script>
@endsection
