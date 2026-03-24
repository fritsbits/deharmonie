@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-8" style="color: var(--color-brand-dark)">Contact</h1>
    <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">
                    {{ app()->getLocale() === 'fr' ? 'Adresse' : 'Adres' }}
                </h2>
                <p>Antwerpsesteenweg 24<br>1000 Brussel</p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">
                    {{ app()->getLocale() === 'fr' ? 'Heures d\'ouverture' : 'Openingsuren' }}
                </h2>
                <p>{{ app()->getLocale() === 'fr' ? 'Lun–Ven' : 'Ma–Vr' }}: 9:30–16:30</p>
                <p>{{ app()->getLocale() === 'fr' ? 'Sam' : 'Za' }}: 10:00–14:00</p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">
                    {{ app()->getLocale() === 'fr' ? 'Téléphone' : 'Telefoon' }}
                </h2>
                <p>
                    <a href="tel:0220328048" class="font-semibold underline" style="color: var(--color-brand-green)">
                        02 203 28 48
                    </a>
                </p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">Email</h2>
                <p>
                    <a href="mailto:info@deharmonie.be" class="font-semibold underline" style="color: var(--color-brand-green)">
                        info@deharmonie.be
                    </a>
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ app()->getLocale() === 'fr' ? 'Activités :' : 'Activiteiten:' }}
                    <a href="mailto:animatie@deharmonie.be" class="underline">animatie@deharmonie.be</a>
                </p>
                <p class="text-sm text-gray-500">
                    {{ app()->getLocale() === 'fr' ? 'Services :' : 'Diensten:' }}
                    <a href="mailto:diensten@deharmonie.be" class="underline">diensten@deharmonie.be</a>
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden min-h-64">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2519.5!2d4.352!3d50.852!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c3c38f0b0b0b0b%3A0x0!2zQW50d2VycHNlc3RlZW53ZWcgMjQsIDEwMDAgQnJ1c3NlbA!5e0!3m2!1snl!2sbe!4v1234567890!5m2!1snl!2sbe"
                class="w-full h-full min-h-64 border-0"
                allowfullscreen
                loading="lazy"
                title="{{ app()->getLocale() === 'fr' ? 'Carte' : 'Kaart' }}">
            </iframe>
        </div>
    </div>
</div>
@endsection
