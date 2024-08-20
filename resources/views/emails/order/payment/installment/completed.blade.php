<x-mail::message>
Hi {{ $walletFunding->buyer->user->name }}! <br>

Your last wallet funding of {{ naira($walletFunding->amount) }} has completed the payment for your order.

Thanks,<br>
Samandcart
</x-mail::message>
