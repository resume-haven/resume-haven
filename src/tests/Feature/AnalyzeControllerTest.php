
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validiert job_text und cv_text mit Mindestlänge', function () {
    $response = $this->post('/analyze', [
        'job_text' => '',
        'cv_text' => '',
    ]);
    $response->assertSessionHasErrors(['job_text', 'cv_text']);
});

it('akzeptiert gültige Eingaben und zeigt die Ergebnis-View', function () {
    $response = $this->post('/analyze', [
        'job_text' => str_repeat('A', 31),
        'cv_text' => str_repeat('B', 31),
    ]);
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('job_text', str_repeat('A', 31));
    $response->assertViewHas('cv_text', str_repeat('B', 31));
});
