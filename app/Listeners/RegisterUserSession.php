<?php

namespace App\Listeners;

use App\Models\UserActiveSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class RegisterUserSession
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = Session::getId();
        $request = Request::instance();

        // Marcar todas las sesiones anteriores como no actuales
        UserActiveSession::where('user_id', $user->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        // Verificar si ya existe esta sesión
        $existingSession = UserActiveSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (! $existingSession) {
            $deviceInfo = $this->parseUserAgent($request->userAgent());
            $location = $this->getLocationFromIp($request->ip());

            UserActiveSession::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_name' => $deviceInfo['device_name'],
                'device_type' => $deviceInfo['device_type'],
                'browser_name' => $deviceInfo['browser_name'],
                'browser_version' => $deviceInfo['browser_version'],
                'operating_system' => $deviceInfo['os_name'],
                'os_version' => $deviceInfo['os_version'],
                'location' => $location,
                'is_current' => true,
                'last_activity' => now(),
            ]);
        } else {
            // Actualizar sesión existente
            $existingSession->update([
                'is_current' => true,
                'last_activity' => now(),
            ]);
        }
    }

    protected function parseUserAgent(?string $userAgent): array
    {
        if (! $userAgent) {
            return $this->getDefaultDeviceInfo();
        }

        $deviceInfo = [
            'device_name' => 'Dispositivo Desconocido',
            'device_type' => 'desktop',
            'browser_name' => 'Desconocido',
            'browser_version' => '',
            'os_name' => 'Desconocido',
            'os_version' => '',
        ];

        // Detectar OS - Orden de detección importante
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $deviceInfo['os_name'] = 'iOS';
            if (preg_match('/OS (\d+)[._](\d+)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = "{$matches[1]}.{$matches[2]}";
            }
            $deviceInfo['device_type'] = preg_match('/iPad/i', $userAgent) ? 'tablet' : 'mobile';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Android';
            if (preg_match('/Android (\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'mobile';
        } elseif (preg_match('/Macintosh|Mac OS X|macOS/i', $userAgent)) {
            $deviceInfo['os_name'] = 'macOS';
            if (preg_match('/Mac OS X (\d+)[._](\d+)/i', $userAgent, $matches)) {
                $version = (int) $matches[1];
                $minor = (int) $matches[2];
                $osNames = [
                    10 => ['10' => 'Yosemite', '11' => 'El Capitan', '12' => 'Sierra'],
                    11 => ['0' => 'Big Sur', '1' => 'Monterey', '2' => 'Ventura', '3' => 'Sonoma'],
                ];
                $deviceInfo['os_version'] = $osNames[$version][$minor] ?? "{$version}.{$minor}";
            } elseif (preg_match('/macOS (\d+)[._](\d+)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = "{$matches[1]}.{$matches[2]}";
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/Windows NT (\d+\.\d+)/i', $userAgent, $matches)) {
            $deviceInfo['os_name'] = 'Windows';
            $versionMap = [
                '10.0' => '10/11',
                '6.3' => '8.1',
                '6.2' => '8',
                '6.1' => '7',
                '6.0' => 'Vista',
                '5.1' => 'XP',
            ];
            $deviceInfo['os_version'] = $versionMap[$matches[1]] ?? $matches[1];
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/Windows Phone/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Windows Phone';
            if (preg_match('/Windows Phone (\d+\.\d+)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'mobile';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Linux';
            // Intentar detectar distribución específica
            if (preg_match('/(Ubuntu|Debian|Fedora|CentOS|Red Hat|SUSE|Arch|Mint|Kali|Gentoo)/i', $userAgent, $matches)) {
                $deviceInfo['os_name'] = $matches[1];
            }
            if (preg_match('/(Ubuntu|Debian|Fedora|CentOS|Red Hat|SUSE|Arch|Mint|Kali|Gentoo)\/(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[2];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/Unix/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Unix';
            if (preg_match('/Unix[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/SunOS|Solaris/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Solaris';
            if (preg_match('/(SunOS|Solaris)[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[2];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/FreeBSD/i', $userAgent)) {
            $deviceInfo['os_name'] = 'FreeBSD';
            if (preg_match('/FreeBSD[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/OpenBSD/i', $userAgent)) {
            $deviceInfo['os_name'] = 'OpenBSD';
            if (preg_match('/OpenBSD[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/NetBSD/i', $userAgent)) {
            $deviceInfo['os_name'] = 'NetBSD';
            if (preg_match('/NetBSD[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/Chrome OS/i', $userAgent)) {
            $deviceInfo['os_name'] = 'Chrome OS';
            if (preg_match('/CrOS[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'desktop';
        } elseif (preg_match('/BlackBerry/i', $userAgent)) {
            $deviceInfo['os_name'] = 'BlackBerry OS';
            if (preg_match('/BlackBerry[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'mobile';
        } elseif (preg_match('/webOS/i', $userAgent)) {
            $deviceInfo['os_name'] = 'webOS';
            if (preg_match('/webOS[\/\s]+(\d+(?:\.\d+)?)/i', $userAgent, $matches)) {
                $deviceInfo['os_version'] = $matches[1];
            }
            $deviceInfo['device_type'] = 'mobile';
        }

        // Detectar navegador
        if (preg_match('/Chrome\/(\d+)/i', $userAgent, $matches)) {
            $deviceInfo['browser_name'] = 'Google Chrome';
            $deviceInfo['browser_version'] = $matches[1];
        } elseif (preg_match('/Safari\/(\d+)/i', $userAgent, $matches) && ! preg_match('/Chrome/i', $userAgent)) {
            $deviceInfo['browser_name'] = 'Safari';
            if (preg_match('/Version\/(\d+)/i', $userAgent, $versionMatch)) {
                $deviceInfo['browser_version'] = $versionMatch[1];
            }
        } elseif (preg_match('/Firefox\/(\d+)/i', $userAgent, $matches)) {
            $deviceInfo['browser_name'] = 'Mozilla Firefox';
            $deviceInfo['browser_version'] = $matches[1];
        } elseif (preg_match('/Edge\/(\d+)/i', $userAgent, $matches)) {
            $deviceInfo['browser_name'] = 'Microsoft Edge';
            $deviceInfo['browser_version'] = $matches[1];
        }

        // Detectar nombre del dispositivo
        if (preg_match('/Macintosh/i', $userAgent)) {
            // Intentar detectar modelo específico de Mac
            if (preg_match('/MacBookPro(\d+),(\d+)/i', $userAgent, $matches)) {
                $deviceInfo['device_name'] = "Macbook Pro {$matches[1]} Retina";
            } elseif (preg_match('/MacBookAir(\d+),(\d+)/i', $userAgent, $matches)) {
                $deviceInfo['device_name'] = "Macbook Air {$matches[1]}";
            } else {
                $deviceInfo['device_name'] = 'Macbook Pro';
            }
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            // Detectar modelo de iPhone
            if (preg_match('/iPhone(\d+),(\d+)/i', $userAgent, $matches)) {
                $models = [
                    '14' => 'iPhone 14',
                    '15' => 'iPhone 15',
                    '13' => 'iPhone 13',
                    '12' => 'iPhone 12',
                    '11' => 'iPhone 11',
                    '10' => 'iPhone X',
                ];
                $deviceInfo['device_name'] = $models[$matches[1]] ?? "iPhone {$matches[1]}";
            } else {
                $deviceInfo['device_name'] = 'iPhone';
            }
        } elseif (preg_match('/iPad/i', $userAgent)) {
            $deviceInfo['device_name'] = 'iPad';
        } elseif (preg_match('/Android/i', $userAgent)) {
            // Detectar modelos Android comunes
            if (preg_match('/(Samsung|Xiaomi|Huawei|OnePlus|Google|Motorola|LG|Sony)\s+([A-Za-z0-9\s]+)/i', $userAgent, $matches)) {
                $deviceInfo['device_name'] = trim($matches[1].' '.$matches[2]);
            } elseif (preg_match('/([A-Za-z0-9\s]+)\s+Build/i', $userAgent, $matches)) {
                $deviceInfo['device_name'] = trim($matches[1]);
            } else {
                $deviceInfo['device_name'] = 'Dispositivo Android';
            }
        }

        return $deviceInfo;
    }

    protected function getDefaultDeviceInfo(): array
    {
        return [
            'device_name' => 'Dispositivo Desconocido',
            'device_type' => 'desktop',
            'browser_name' => 'Desconocido',
            'browser_version' => '',
            'os_name' => 'Desconocido',
            'os_version' => '',
        ];
    }

    protected function getLocationFromIp(?string $ip): string
    {
        if (! $ip || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local, Chile';
        }

        // Placeholder - en producción usar servicio de geolocalización
        return 'Santiago, Chile';
    }
}
