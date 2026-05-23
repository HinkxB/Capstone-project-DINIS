namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $table = 'document';
    protected $primaryKey = 'document_id';
    public $timestamps = false;

    protected $fillable = [
        'person_id', 'document_type', 'document_no', 'issue_date', 
        'issuing_authority', 'file_ref', 'hash_value', 'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'person_id');
    }
}
