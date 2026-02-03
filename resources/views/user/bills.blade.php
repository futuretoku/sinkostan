<h2>Tagihan Saya</h2>

@foreach($bills as $bill)
  <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px">
    <b>Kamar:</b> {{ $bill->booking->room->room_number }} <br>
    <b>Jatuh Tempo:</b> {{ $bill->due_date }} <br>
    <b>Nominal:</b> Rp {{ number_format($bill->amount) }} <br>
    <b>Status:</b> {{ strtoupper($bill->status) }}
  </div>
@endforeach

@if($bill->status === 'unpaid')
  <a href="/bill/{{ $bill->id }}/pay">Bayar</a>
@else
  <span>{{ strtoupper($bill->status) }}</span>
@endif

