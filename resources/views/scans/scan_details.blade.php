@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        @if(!$scan)
            <div class="flex justify-center items-center text-gray-600 italic">
                Scan not found.
            </div>
        @else
            <h2 class="text-xl font-bold mb-4">Scan {{$scan->id}} | {{ $scan->occurred_at->format('d-m-Y H:i:s') }}</h2>

            <table class="bg-white border border-gray-300">
                <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Customer ID</th>
                    <th class="px-4 py-2">First name</th>
                    <th class="px-4 py-2">Last name</th>
                    <th class="px-4 py-2">Date of birth</th>
                    <th class="px-4 py-2">Phone number</th>
                    <th class="px-4 py-2">IBAN</th>
                    <th class="px-4 py-2">IP Address</th>
                    <th class="px-4 py-2">Fraud Reasons</th>
                </tr>
                </thead>
                <tbody>
                @foreach($scan->customers as $customer)
                    @php
                        $isFraudulent = $customer->fraudReasons->isNotEmpty();
                    @endphp
                    <tr class="{{ $isFraudulent ? 'bg-red-100' : 'bg-white' }} border-b">
                        <td class="px-4 py-2">{{ $customer->customer_id }}</td>
                        <td class="px-4 py-2 ">{{ $customer->first_name }}</td>
                        <td class="px-4 py-2">{{ $customer->last_name }}</td>
                        <td class="px-4 py-2">{{ $customer->date_of_birth->format('d-m-Y') }}</td>
                        <td class="px-4 py-2">{{ $customer->phone_number }}</td>
                        <td class="px-4 py-2">{{ $customer->iban }}</td>
                        <td class="px-4 py-2">{{ $customer->ip_address }}</td>
                        <td class="px-4 py-2">
                            @if($isFraudulent)
                                <ul class="list-disc ml-4 text-sm">
                                    @foreach($customer->fraudReasons as $reason)
                                        <li>
                                            <strong>{{ $reason->code }}</strong> – {{ $reason->pivot->context }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500 italic">None</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
