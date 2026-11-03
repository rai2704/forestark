@php $panelSite = App\Support\Site::settings(); @endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<link rel="icon" href="{{ media($panelSite['favicon']) }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
@include('partials.a11y-head')
<style>:root:root { --moc-primary: {{ $panelSite['color_footer'] ?: '#1c4e1f' }}; --moc-primary-dk: {{ $panelSite['color_header'] ?: '#163e19' }}; --moc-link: {{ $panelSite['color_brand'] ?: '#379c3e' }}; }</style>
