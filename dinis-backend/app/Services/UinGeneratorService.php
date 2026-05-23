namespace App\Services;

use App\Models\Person;
use App\Models\NrcCard;

class UinGeneratorService
{
    /**
     * Generates a 10-digit UIN (e.g., 1000000001)
     */
    public function generateUin(): string
    {
        // For a POC, we find the max UIN and increment, starting at 1000000000
        $latest = Person::max('uin');
        $next = $latest ? ((int)$latest + 1) : 1000000000;
        
        return (string) $next;
    }

    /**
     * Generates a standard NRC format (e.g., 123456/10/1)
     */
    public function generateNrcNumber(int $provinceCode): string
    {
        // Format: [6 random digits] / [Province Code] / [Checksum or Version]
        $random = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        $version = 1; // Default to 1 for first time issue
        
        return "{$random}/{$provinceCode}/{$version}";
    }
}
