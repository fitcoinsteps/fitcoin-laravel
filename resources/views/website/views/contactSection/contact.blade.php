@extends('layouts.guest')

@section('title', 'Contact - Fitcoin')

@section('content')
<div class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-600/10 border border-indigo-600/30 mb-6">
            <span class="text-sm text-indigo-400">Get in Touch</span>
        </div>

        <h1 class="text-4xl sm:text-5xl font-bold mb-6">
            Contact <span class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">Us</span>
        </h1>

        <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-12">
            Have questions? We'd love to hear from you. Reach out to us anytime.
        </p>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-indigo-600/10 border border-indigo-600/30">
                    <i class="fas fa-envelope text-2xl text-indigo-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Email</h3>
                <p class="text-gray-400 text-sm">support@fitcoin.com</p>
            </div>

            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-purple-600/10 border border-purple-600/30">
                    <i class="fas fa-phone text-2xl text-purple-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Phone</h3>
                <p class="text-gray-400 text-sm">+1 (555) 123-4567</p>
            </div>

            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-pink-600/10 border border-pink-600/30">
                    <i class="fas fa-map-marker-alt text-2xl text-pink-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Location</h3>
                <p class="text-gray-400 text-sm">New York, NY</p>
            </div>
        </div>
    </div>
</div>
@endsection