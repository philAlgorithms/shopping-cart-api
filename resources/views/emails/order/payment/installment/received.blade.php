<x-mail::message>
Hi {{ $walletFunding->buyer->user->name }}! <br>

Your wallet has been funded with {{ naira($walletFunding->amount) }}.

Thanks,<br>
Samandcart
</x-mail::message>