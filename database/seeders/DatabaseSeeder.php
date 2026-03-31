use App\Models\User;
use Spatie\Permission\Models\Role;

public function run(): void
{
// Create admin role if it doesn't exist
$adminRole = Role::firstOrCreate(['name' => 'admin']);

// Ensure Admin user exists
$adminUser = User::firstOrCreate(
['email' => 'admin@studymate.com'],
[
'name' => 'Admin',
'password' => bcrypt('password'), // change to secure password
]
);

// Assign role
$adminUser->assignRole($adminRole);
}