{{-- Renders nothing when $data is null (block switched off in admin).
     JSON_HEX_* escapes <, >, & and quotes so user content can't close the script tag. --}}
@if(!empty($data))
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
