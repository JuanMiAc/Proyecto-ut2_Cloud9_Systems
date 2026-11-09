<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        // Precios enteros truncados desde el INSERT original (sin redondear).
        $filas = [
            // AWS Computación
            [1, 1, 1, 'EC2', 'Servidor virtual AWS', 40, true],
            [1, 2, 1, 'EC2', 'Servidor virtual AWS', 42, true],
            [1, 3, 1, 'EC2', 'Servidor virtual AWS', 44, true],
            [1, 1, 1, 'Lightsail', 'Servidor sencillo AWS', 5, true],
            [1, 2, 1, 'Lightsail', 'Servidor sencillo AWS', 5, false], // 5.25 -> 5
            [1, 3, 1, 'Lightsail', 'Servidor sencillo AWS', 5, true], // 5.50 -> 5
            // AWS Almacenamiento
            [1, 1, 2, 'S3', 'Almacenamiento de objetos', 0, true], // 0.02 -> 0
            [1, 2, 2, 'S3', 'Almacenamiento de objetos', 0, true],
            [1, 3, 2, 'S3', 'Almacenamiento de objetos', 0, true],
            // AWS Bases de datos
            [1, 1, 3, 'RDS', 'Base de datos gestionada', 50, true],
            [1, 2, 3, 'RDS', 'Base de datos gestionada', 52, true], // 52.5 -> 52
            [1, 3, 3, 'RDS', 'Base de datos gestionada', 55, false],
            [1, 1, 3, 'DynamoDB', 'Base de datos NoSQL', 15, true],
            [1, 2, 3, 'DynamoDB', 'Base de datos NoSQL', 15, true], // 15.75 -> 15
            [1, 3, 3, 'DynamoDB', 'Base de datos NoSQL', 16, true], // 16.5 -> 16
            // AWS Redes
            [1, 1, 4, 'VPC', 'Red privada virtual', 0, true],
            [1, 2, 4, 'VPC', 'Red privada virtual', 0, true],
            [1, 3, 4, 'VPC', 'Red privada virtual', 0, true],
            // AWS Seguridad
            [1, 1, 5, 'IAM', 'Gestion de identidades', 0, true],
            [1, 2, 5, 'IAM', 'Gestion de identidades', 0, true],
            [1, 3, 5, 'IAM', 'Gestion de identidades', 0, true],
            // AWS Monitorización
            [1, 1, 6, 'CloudWatch', 'Monitorizacion AWS', 10, true],
            [1, 2, 6, 'CloudWatch', 'Monitorizacion AWS', 10, false], // 10.5 -> 10
            [1, 3, 6, 'CloudWatch', 'Monitorizacion AWS', 11, true],
            // Azure Computación
            [2, 4, 1, 'Virtual Machines', 'Maquinas virtuales', 38, true],
            [2, 5, 1, 'Virtual Machines', 'Maquinas virtuales', 36, true], // 36.1 -> 36
            // Azure Almacenamiento
            [2, 4, 2, 'Blob Storage', 'Almacenamiento de objetos', 0, true], // 0.015 -> 0
            [2, 5, 2, 'Blob Storage', 'Almacenamiento de objetos', 0, true], // 0.01425 -> 0
            [2, 4, 2, 'Managed Disks', 'Discos virtuales', 8, true],
            [2, 5, 2, 'Managed Disks', 'Discos virtuales', 7, false], // 7.6 -> 7
            // Azure Bases de datos
            [2, 4, 3, 'Azure SQL Database', 'Base de datos SQL', 45, true],
            [2, 5, 3, 'Azure SQL Database', 'Base de datos SQL', 42, true], // 42.75 -> 42
            // Azure Redes
            [2, 4, 4, 'Virtual Network', 'Red virtual Azure', 0, true],
            [2, 5, 4, 'Virtual Network', 'Red virtual Azure', 0, true],
            // Azure Seguridad
            [2, 4, 5, 'Key Vault', 'Gestion de claves', 3, true],
            [2, 5, 5, 'Key Vault', 'Gestion de claves', 2, true], // 2.85 -> 2
            // Azure Monitorización
            [2, 4, 6, 'Azure Monitor', 'Monitorizacion Azure', 12, true],
            [2, 5, 6, 'Azure Monitor', 'Monitorizacion Azure', 11, true], // 11.4 -> 11
        ];

        foreach ($filas as [$proveedorId, $regionId, $tipoId, $nombre, $descripcion, $precio, $disponible]) {
            Producto::query()->create([
                'proveedor_id' => $proveedorId,
                'region_id' => $regionId,
                'tipo_id' => $tipoId,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'disponible' => $disponible,
                'protegido' => true,
            ]);
        }
    }
}
