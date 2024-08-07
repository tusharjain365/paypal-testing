<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Display total credits -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Total Credits</h3>
                    <p class="text-xl">{{ Auth::user()->credits }} credits</p>
                </div>
            </div>

            <!-- PayPal payment form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Add Credits</h3>
                    <form action="{{ url('/paypal/payment') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Select Plan:</label>
                            <select id="amount" name="amount" class="form-select mt-1 block w-full">
                                <option value="49">Monthly Plan - $49</option>
                                <option value="456">Yearly Plan - $456</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Pay with PayPal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
