<?php

namespace App\Support;

class Catalogo
{
    public const PROVEEDORES = [
        1 => 'AWS',
        2 => 'Azure',
    ];

    public const REGIONES = [
        1 => 'Irlanda',
        2 => 'Frankfurt',
        3 => 'Londres',
        4 => 'West Europe',
        5 => 'North Europe',
    ];

    public const TIPOS = [
        1 => 'Computación',
        2 => 'Almacenamiento',
        3 => 'Bases de datos',
        4 => 'Redes',
        5 => 'Seguridad',
        6 => 'Monitorización',
    ];

    public const REGIONES_POR_PROVEEDOR = [
        1 => [1, 2, 3],
        2 => [4, 5],
    ];

    public const TIPOS_PAGO = [
        'Pago íntegro',
        'Fraccionado',
    ];

    public static function proveedor(?int $id): ?string
    {
        return $id === null ? null : (self::PROVEEDORES[$id] ?? null);
    }

    public static function region(?int $id): ?string
    {
        return $id === null ? null : (self::REGIONES[$id] ?? null);
    }

    public static function tipo(?int $id): ?string
    {
        return $id === null ? null : (self::TIPOS[$id] ?? null);
    }

    public static function regionesDeProveedor(int $proveedorId): array
    {
        $ids = self::REGIONES_POR_PROVEEDOR[$proveedorId] ?? [];

        return array_intersect_key(self::REGIONES, array_flip($ids));
    }

    public static function regionCompatible(int $proveedorId, int $regionId): bool
    {
        return in_array($regionId, self::REGIONES_POR_PROVEEDOR[$proveedorId] ?? [], true);
    }

    public static function proveedorIds(): array
    {
        return array_keys(self::PROVEEDORES);
    }

    public static function regionIds(): array
    {
        return array_keys(self::REGIONES);
    }

    public static function tipoIds(): array
    {
        return array_keys(self::TIPOS);
    }
}
