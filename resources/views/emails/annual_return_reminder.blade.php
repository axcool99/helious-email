@php
    $blocks = preg_split("/\R{2,}/u", trim($body)) ?: [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mailSubject ?? 'Annual Return Reminder' }}</title>
</head>
<body style="margin: 0; padding: 24px; background: #f4f7fb; color: #111827; font-family: Arial, Helvetica, sans-serif; line-height: 1.7;">
    <div style="max-width: 760px; margin: 0 auto; background: #ffffff; border: 1px solid #dbe3ee; border-radius: 16px; padding: 32px;">
        @foreach ($blocks as $block)
            @php
                $lines = preg_split("/\R/u", trim($block)) ?: [];
                $trimmedLines = array_map(static fn (string $line): string => trim($line), $lines);
                $isSubtitle = count($trimmedLines) === 1
                    && preg_match('/^_(.+)_$/u', $trimmedLines[0], $subtitleMatch);
                $isBulletList = count($trimmedLines) > 0
                    && collect($trimmedLines)->every(static fn (string $line): bool => str_starts_with($line, '•'));
            @endphp

            @if ($isSubtitle)
                <p style="margin: 0 0 20px; font-size: 16px; font-weight: 700;">
                    {{ $subtitleMatch[1] }}
                </p>
            @elseif ($isBulletList)
                <ul style="margin: 0 0 20px; padding-left: 22px;">
                    @foreach ($trimmedLines as $line)
                        <li style="margin: 0 0 8px;">
                            {{ ltrim(mb_substr($line, 1), " \t\n\r\0\x0B") }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="margin: 0 0 20px;">
                    {!! nl2br(e(implode("\n", $trimmedLines))) !!}
                </p>
            @endif
        @endforeach
    </div>
</body>
</html>
