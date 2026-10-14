<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function active_nav(string $current, string $target): string
{
    return $current === $target ? ' class="active"' : '';
}

function fetch_all_assoc(mysqli_stmt $stmt): array
{
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function first_or_null(mysqli_stmt $stmt): ?array
{
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    return $row ?: null;
}

function money(float|int|string $amount): string
{
    return '$' . number_format((float) $amount, 2);
}

function excerpt(string $text, int $limit = 130): string
{
    $clean = trim($text);
    if (strlen($clean) <= $limit) {
        return $clean;
    }

    return rtrim(substr($clean, 0, $limit - 3)) . '...';
}

function date_days(string $start, string $end): int
{
    $startDate = new DateTimeImmutable($start);
    $endDate = new DateTimeImmutable($end);
    $days = (int) $startDate->diff($endDate)->format('%a');
    return max(1, $days);
}

function booking_total(array $package, ?array $accommodation, ?array $vehicle, ?array $guide, int $travelers, string $start, string $end): float
{
    $days = date_days($start, $end);
    $total = (float) $package['base_price'] * max(1, $travelers);

    if ($accommodation) {
        $total += (float) $accommodation['price_per_night'] * $days;
    }

    if ($vehicle) {
        $total += (float) $vehicle['price_per_day'] * $days;
    }

    if ($guide) {
        $total += (float) $guide['price_per_day'] * $days;
    }

    return $total;
}

function status_label(string $status): string
{
    $label = str_replace('_', ' ', $status);
    return ucwords($label);
}

function image_url(?string $image, string $fallback = 'assets/images/hero-beach.png'): string
{
    return $image ?: $fallback;
}
