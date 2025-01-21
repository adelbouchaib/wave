<?php
    use function Laravel\Folio\{name};
    name('home');
    // Redirect if the current URL is `/`
    if (request()->is('/')) {
        header("Location: /login");
        exit;
    }


?>

<x-layouts.marketing
    :seo="[
        'title'         => setting('site.title', 'Spysouk'),
        'description'   => setting('site.description', 'Software as a Service Starter Kit'),
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>
        <x-marketing.sections.hero />
        
        <x-container class="py-12 border-t sm:py-24 border-zinc-200">
            <x-marketing.sections.features />
        </x-container>

        <x-container class="py-12 border-t sm:py-24 border-zinc-200">
            <x-marketing.sections.testimonials />
        </x-container>
        
        <x-container class="py-12 border-t sm:py-24 border-zinc-200">
            <x-marketing.sections.pricing />
        </x-container>

</x-layouts.marketing>
