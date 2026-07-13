import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;
import 'package:webview_flutter/webview_flutter.dart';
import 'package:url_launcher/url_launcher.dart';

int parseInt(dynamic value) {
  if (value == null) return 0;
  if (value is num) return value.toInt();
  if (value is String) {
    return double.tryParse(value)?.toInt() ?? int.tryParse(value) ?? 0;
  }
  return 0;
}

double parseDouble(dynamic value) {
  if (value == null) return 0.0;
  if (value is num) return value.toDouble();
  if (value is String) {
    return double.tryParse(value) ?? 0.0;
  }
  return 0.0;
}

void main() {
  runApp(const LaundraiApp());
}

// Global Configuration
class AppConfig {
  // GANTI IP INI DENGAN IP LAPTOP ANDA (Cek ipconfig di CMD)
  // Contoh: 'http://192.168.1.9:8000'
  static String baseUrl = 'https://l-dry.my.id';
  static String? token;
  static Map<String, dynamic>? userProfile;
  static String? preSelectedService;
}

class LaundraiApp extends StatelessWidget {
  const LaundraiApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'L-DRY Mobile',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        fontFamily: 'Outfit',
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF2563EB),
          primary: const Color(0xFF2563EB),
          secondary: const Color(0xFF1E3A8A),
          surface: const Color(0xFFF8FAFC),
        ),
        scaffoldBackgroundColor: const Color(0xFFF8FAFC),
      ),
      home: const AuthWrapper(),
    );
  }
}

// Router Wrapper to check authentication status
class AuthWrapper extends StatefulWidget {
  const AuthWrapper({super.key});

  @override
  State<AuthWrapper> createState() => _AuthWrapperState();
}

class _AuthWrapperState extends State<AuthWrapper> {
  @override
  Widget build(BuildContext context) {
    if (AppConfig.token == null) {
      return const WelcomeScreen();
    } else {
      return const MainNavigationScreen();
    }
  }
}

// ======================== WELCOME SCREEN ========================
class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFFEFF6FF), Color(0xFFDBEAFE)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 30.0),
            child: Column(
              children: [
                const Spacer(flex: 2),
                
                // Logo section with glassmorphism container
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.6),
                    borderRadius: BorderRadius.circular(32),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.blue.shade100.withValues(alpha: 0.5),
                        blurRadius: 20,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: Image.network(
                    'https://l-dry.my.id/logo.png',
                    height: 160,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) => Container(
                      width: 96,
                      height: 96,
                      decoration: BoxDecoration(
                        color: const Color(0xFF2563EB),
                        borderRadius: BorderRadius.circular(24),
                      ),
                      child: const Icon(
                        Icons.local_laundry_service,
                        color: Colors.white,
                        size: 48,
                      ),
                    ),
                  ),
                ),
                
                const Spacer(flex: 1),
                
                // Content text section
                const Text(
                  'Selamat Datang di L-DRY',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 26,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF0F172A),
                    letterSpacing: 0.5,
                  ),
                ),
                const SizedBox(height: 12),
                const Text(
                  'Layanan cuci premium, cepat, bersih, dan harum. Kami jemput cucian kotor Anda dan antar kembali dalam kondisi bersih sempurna.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xFF475569),
                    height: 1.6,
                  ),
                ),
                
                const Spacer(flex: 2),
                
                // Premium value cards
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _buildFeatureIcon(Icons.delivery_dining_outlined, 'Antar Jemput'),
                    _buildFeatureIcon(Icons.timer_outlined, 'Cepat & Wangi'),
                    _buildFeatureIcon(Icons.payment_outlined, 'Bayar Mudah'),
                  ],
                ),
                
                const Spacer(flex: 2),
                
                // CTA Action button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const LoginScreen()),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2563EB),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      elevation: 4,
                      shadowColor: const Color(0xFF2563EB).withValues(alpha: 0.4),
                    ),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          'Mulai Sekarang',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        SizedBox(width: 8),
                        Icon(Icons.arrow_forward_rounded),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildFeatureIcon(IconData icon, String label) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            shape: BoxShape.circle,
            boxShadow: [
              BoxShadow(
                color: Colors.blue.shade50.withValues(alpha: 0.6),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Icon(icon, color: const Color(0xFF2563EB), size: 24),
        ),
        const SizedBox(height: 8),
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w700,
            color: Color(0xFF1E293B),
          ),
        ),
      ],
    );
  }
}

// ======================== LOGIN SCREEN ========================
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController(
    text: 'customer1@example.com',
  );
  final TextEditingController _passwordController = TextEditingController(
    text: 'password',
  );
  bool _isLoading = false;
  String _errorMessage = '';

  Future<void> _handleLogin() async {
    setState(() {
      _isLoading = true;
      _errorMessage = '';
    });

    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': _emailController.text.trim(),
          'password': _passwordController.text,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        AppConfig.token = data['token'];
        AppConfig.userProfile = data['user'];

        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder: (context) => const MainNavigationScreen(),
            ),
          );
        }
      } else {
        setState(() {
          _errorMessage = data['message'] ?? 'Email atau password salah.';
        });
      }
    } catch (e) {
      setState(() {
        _errorMessage =
            'Gagal terhubung ke server. Pastikan Host/IP benar & server Laravel aktif.';
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 30),
              Center(
                child: Image.network(
                  'https://l-dry.my.id/logo.png',
                  height: 140,
                  fit: BoxFit.contain,
                  errorBuilder: (context, error, stackTrace) => Column(
                    children: [
                      Container(
                        width: 64,
                        height: 64,
                        decoration: BoxDecoration(
                          color: const Color(0xFF2563EB),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Icon(
                          Icons.local_laundry_service,
                          color: Colors.white,
                          size: 32,
                        ),
                      ),
                      const SizedBox(height: 10),
                      const Text(
                        'L-DRY LAUNDRY',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFF0F172A),
                          letterSpacing: 1.5,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 8),
              const Center(
                child: Text(
                  'Masuk untuk memesan & melacak laundry Anda',
                  style: TextStyle(color: Color(0xFF64748B), fontSize: 13),
                ),
              ),
              const SizedBox(height: 30),

              if (_errorMessage.isNotEmpty) ...[
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Text(
                    _errorMessage,
                    style: TextStyle(
                      color: Colors.red.shade800,
                      fontSize: 12,
                      height: 1.4,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],



              const Text(
                'EMAIL',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: InputDecoration(
                  hintText: 'nama@email.com',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),

              const Text(
                'PASSWORD',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _passwordController,
                obscureText: true,
                decoration: InputDecoration(
                  hintText: '••••••••',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 30),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _handleLogin,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563EB),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text(
                          'Masuk',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 24),

              Center(
                child: TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const RegisterScreen(),
                      ),
                    );
                  },
                  child: const Text(
                    'Belum punya akun? Daftar Sekarang',
                    style: TextStyle(
                      color: Color(0xFF2563EB),
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ======================== REGISTER SCREEN ========================
class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _isLoading = false;
  String _errorMessage = '';

  Future<void> _handleRegister() async {
    setState(() {
      _isLoading = true;
      _errorMessage = '';
    });

    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/register'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'name': _nameController.text.trim(),
          'email': _emailController.text.trim(),
          'phone': _phoneController.text.trim(),
          'address': _addressController.text.trim(),
          'password': _passwordController.text,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 201 && data['success'] == true) {
        AppConfig.token = data['token'];
        AppConfig.userProfile = data['user'];

        if (mounted) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(
              builder: (context) => const MainNavigationScreen(),
            ),
            (route) => false,
          );
        }
      } else {
        setState(() {
          _errorMessage = data['message'] ?? 'Pendaftaran gagal.';
        });
      }
    } catch (e) {
      setState(() {
        _errorMessage = 'Gagal terhubung ke server.';
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Akun Baru'),
        surfaceTintColor: Colors.transparent,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (_errorMessage.isNotEmpty) ...[
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Text(
                    _errorMessage,
                    style: TextStyle(
                      color: Colors.red.shade800,
                      fontSize: 12,
                      height: 1.4,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],

              const Text(
                'NAMA LENGKAP',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _nameController,
                decoration: InputDecoration(
                  hintText: 'Nama lengkap Anda',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),

              const Text(
                'EMAIL',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: InputDecoration(
                  hintText: 'nama@email.com',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),

              const Text(
                'NOMOR WHATSAPP',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  hintText: 'Contoh: 08123456789',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),

              const Text(
                'ALAMAT LENGKAP',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _addressController,
                maxLines: 2,
                decoration: InputDecoration(
                  hintText: 'Nomor rumah, jalan, kecamatan',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),

              const Text(
                'PASSWORD',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: _passwordController,
                obscureText: true,
                decoration: InputDecoration(
                  hintText: 'Minimal 8 karakter',
                  fillColor: Colors.white,
                  filled: true,
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 32),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _handleRegister,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563EB),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    elevation: 0,
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text(
                          'Daftar',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ======================== MAIN NAVIGATION SCREEN ========================
class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  static dynamic of(BuildContext context) =>
      context.findAncestorStateOfType<_MainNavigationScreenState>();

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;
  final List<Widget> _screens = [
    const HomeScreen(),
    const CreateOrderScreen(),
    const HistoryScreen(),
    const ProfileScreen(),
  ];

  void setTab(int index) {
    setState(() {
      _currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: _screens),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 20,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: NavigationBar(
          selectedIndex: _currentIndex,
          onDestinationSelected: (index) {
            setState(() {
              _currentIndex = index;
            });
          },
          backgroundColor: Colors.white,
          indicatorColor: const Color(0xFF2563EB).withValues(alpha: 0.1),
          destinations: const [
            NavigationDestination(
              icon: Icon(Icons.grid_view_outlined, color: Color(0xFF64748B)),
              selectedIcon: Icon(Icons.grid_view_rounded, color: Color(0xFF2563EB)),
              label: 'Home',
            ),
            NavigationDestination(
              icon: Icon(Icons.add_circle_outline, color: Color(0xFF64748B)),
              selectedIcon: Icon(Icons.add_circle, color: Color(0xFF2563EB)),
              label: 'Orders',
            ),
            NavigationDestination(
              icon: Icon(Icons.assignment_outlined, color: Color(0xFF64748B)),
              selectedIcon: Icon(Icons.assignment, color: Color(0xFF2563EB)),
              label: 'History',
            ),
            NavigationDestination(
              icon: Icon(Icons.person_outline, color: Color(0xFF64748B)),
              selectedIcon: Icon(Icons.person, color: Color(0xFF2563EB)),
              label: 'Profile',
            ),
          ],
        ),
      ),
    );
  }
}

// ======================== HOME SCREEN ========================
class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _totalOrders = 0;
  int _activeOrders = 0;
  int _completedOrders = 0;
  int _points = 0;
  int _unreadNotificationsCount = 0;
  List<dynamic> _activeOrdersList = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    if (AppConfig.token == null) return;
    if (mounted) setState(() => _isLoading = true);

    try {
      // 1. Fetch profile points
      final profileRes = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/user'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (profileRes.statusCode == 200) {
        final profileData = jsonDecode(profileRes.body);
        if (profileData['success'] == true) {
          AppConfig.userProfile = profileData['user'];
          _points = parseInt(AppConfig.userProfile?['points']);
        }
      }

      // 2. Fetch orders to calculate stats
      final ordersRes = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/orders'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (ordersRes.statusCode == 200) {
        final ordersData = jsonDecode(ordersRes.body);
        if (ordersData['success'] == true) {
          final List ordersList = ordersData['orders'] ?? [];
          _totalOrders = ordersList.length;
          _activeOrdersList = ordersList.where((o) => o['status'] != 'delivered' && o['status'] != 'cancelled').toList();
          _activeOrders = _activeOrdersList.length;
          _completedOrders = ordersList.where((o) => o['status'] == 'delivered').toList().length;
        }
      }

      // 3. Fetch notifications for badge
      final notifRes = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/notifications'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (notifRes.statusCode == 200) {
        final notifData = jsonDecode(notifRes.body);
        _unreadNotificationsCount = parseInt(notifData['unread_count']);
      }
    } catch (e) {
      // catch silently
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  String _formatPoints(int pts) {
    final RegExp reg = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    return pts.toString().replaceAllMapped(reg, (Match match) => '${match[1]},');
  }

  @override
  Widget build(BuildContext context) {
    final name = AppConfig.userProfile?['name'] ?? 'Pelanggan';
    final initials = name
        .substring(0, name.length >= 2 ? 2 : name.length)
        .toUpperCase();

    double progress = _points / 1500;
    if (progress > 1.0) progress = 1.0;

    return Scaffold(
      body: SafeArea(
        child: RefreshIndicator(
          color: const Color(0xFF2563EB),
          onRefresh: _loadDashboardData,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // L-DRY Header (Logo on left, bell & avatar on right)
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Image.network(
                        'https://l-dry.my.id/logo.png',
                        height: 30,
                        fit: BoxFit.contain,
                        errorBuilder: (context, error, stackTrace) => Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(6),
                              decoration: BoxDecoration(
                                color: const Color(0xFF2563EB).withOpacity(0.1),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.change_circle_outlined,
                                color: Color(0xFF2563EB),
                                size: 18,
                              ),
                            ),
                            const SizedBox(width: 6),
                            const Text(
                              'L-DRY',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: Color(0xFF1E3A8A),
                                letterSpacing: 1.0,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Row(
                        children: [
                          // Bell icon with dynamic badge
                          Stack(
                            children: [
                              IconButton(
                                icon: const Icon(
                                  Icons.notifications_none_rounded,
                                  color: Color(0xFF0F172A),
                                  size: 26,
                                ),
                                onPressed: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => const NotificationListScreen(),
                                    ),
                                  ).then((_) => _loadDashboardData());
                                },
                              ),
                              if (_unreadNotificationsCount > 0)
                                Positioned(
                                  right: 12,
                                  top: 12,
                                  child: Container(
                                    width: 8,
                                    height: 8,
                                    decoration: const BoxDecoration(
                                      color: Colors.redAccent,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                                ),
                            ],
                          ),
                          const SizedBox(width: 4),
                          // Avatar
                          GestureDetector(
                            onTap: () {
                              MainNavigationScreen.of(context)?.setTab(3);
                            },
                            child: Container(
                              width: 36,
                              height: 36,
                              decoration: BoxDecoration(
                                color: const Color(0xFF2563EB).withOpacity(0.1),
                                shape: BoxShape.circle,
                              ),
                              child: Center(
                                child: Text(
                                  initials,
                                  style: const TextStyle(
                                    color: Color(0xFF2563EB),
                                    fontWeight: FontWeight.bold,
                                    fontSize: 14,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Greeting Section
                  const Text(
                    'Halo,',
                    style: TextStyle(
                      fontSize: 14,
                      color: Color(0xFF64748B),
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  Text(
                    name,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF0F172A),
                      height: 1.1,
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Pakaian bersih Anda hanya sejauh satu ketukan.',
                    style: TextStyle(
                      fontSize: 13,
                      color: Color(0xFF64748B),
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Loyalty Reward Card
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF2563EB), Color(0xFF1D4ED8)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(28),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF2563EB).withOpacity(0.25),
                          blurRadius: 20,
                          offset: const Offset(0, 10),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'L-DRY SILVER MEMBER',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.8),
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 1.0,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.all(6),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.2),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.star_rounded,
                                color: Colors.white,
                                size: 16,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          '${_formatPoints(_points)} Points',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 32,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 20),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Progress ke Gold',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.9),
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              '$_points/1500',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.9),
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        ClipRRect(
                          borderRadius: BorderRadius.circular(4),
                          child: LinearProgressIndicator(
                            value: progress,
                            backgroundColor: Colors.white.withOpacity(0.25),
                            valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
                            minHeight: 6,
                          ),
                        ),
                        const SizedBox(height: 20),
                        SizedBox(
                          width: double.infinity,
                          child: OutlinedButton(
                            onPressed: () {
                              Navigator.push(
                                context,
                                  MaterialPageRoute(
                                    builder: (context) => const RewardsScreen(),
                                  ),
                                ).then((_) => _loadDashboardData());
                              },
                              style: OutlinedButton.styleFrom(
                                foregroundColor: Colors.white,
                                side: BorderSide(color: Colors.white.withOpacity(0.6)),
                                padding: const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                              ),
                              child: const Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(
                                    'Tukarkan Reward',
                                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                  ),
                                  SizedBox(width: 6),
                                  Icon(Icons.arrow_forward, size: 16),
                                ],
                              ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 28),

                  // Pesanan Aktif Section
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Pesanan Aktif',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0F172A),
                        ),
                      ),
                      GestureDetector(
                        onTap: () {
                          MainNavigationScreen.of(context)?.setTab(2);
                        },
                        child: const Text(
                          'LIHAT SEMUA',
                          style: TextStyle(
                            fontSize: 12,
                            color: Color(0xFF2563EB),
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Horizontal Active Orders Slider
                  _isLoading
                      ? const Center(
                          child: Padding(
                            padding: EdgeInsets.all(20.0),
                            child: CircularProgressIndicator(color: Color(0xFF2563EB)),
                          ),
                        )
                      : _activeOrdersList.isEmpty
                          ? Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(20),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(24),
                                border: Border.all(color: const Color(0xFFF1F5F9)),
                              ),
                              child: const Column(
                                children: [
                                  Icon(Icons.local_laundry_service_outlined, size: 36, color: Color(0xFF94A3B8)),
                                  SizedBox(height: 8),
                                  Text(
                                    'Tidak ada pesanan aktif saat ini.\nYuk mulai pesan laundry Anda!',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(color: Color(0xFF64748B), fontSize: 12, height: 1.4),
                                  ),
                                ],
                              ),
                            )
                          : SizedBox(
                              height: 160,
                              child: ListView.builder(
                                scrollDirection: Axis.horizontal,
                                itemCount: _activeOrdersList.length,
                                itemBuilder: (context, idx) {
                                  final order = _activeOrdersList[idx];
                                  final isPickup = order['delivery_method'] == 'pickup';

                                  return GestureDetector(
                                    onTap: () {
                                      Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (context) => OrderDetailScreen(orderId: order['id']),
                                        ),
                                      ).then((_) => _loadDashboardData());
                                    },
                                    child: Container(
                                      width: 290,
                                      margin: const EdgeInsets.only(right: 16, bottom: 4),
                                      padding: const EdgeInsets.all(16),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(24),
                                        border: Border.all(color: const Color(0xFFF1F5F9)),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black.withOpacity(0.02),
                                            blurRadius: 8,
                                            offset: const Offset(0, 4),
                                          ),
                                        ],
                                      ),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: const Color(0xFF2563EB).withOpacity(0.1),
                                                  borderRadius: BorderRadius.circular(6),
                                                ),
                                                child: Text(
                                                  '#${order['order_number']}',
                                                  style: const TextStyle(
                                                    color: Color(0xFF2563EB),
                                                    fontWeight: FontWeight.bold,
                                                    fontSize: 11,
                                                  ),
                                                ),
                                              ),
                                              Text(
                                                order['status_label'] ?? 'Diproses',
                                                style: const TextStyle(
                                                  color: Color(0xFFF59E0B),
                                                  fontSize: 11,
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 8),
                                          Row(
                                            children: [
                                              Container(
                                                padding: const EdgeInsets.all(8),
                                                decoration: BoxDecoration(
                                                  color: const Color(0xFF2563EB).withOpacity(0.08),
                                                  shape: BoxShape.circle,
                                                ),
                                                child: Icon(
                                                  isPickup ? Icons.local_shipping : Icons.storefront,
                                                  color: const Color(0xFF2563EB),
                                                  size: 20,
                                                ),
                                              ),
                                              const SizedBox(width: 12),
                                              Expanded(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text(
                                                      order['service_name'] ?? 'Laundry',
                                                      style: const TextStyle(
                                                        fontWeight: FontWeight.bold,
                                                        fontSize: 13,
                                                        color: Color(0xFF0F172A),
                                                      ),
                                                    ),
                                                    Text(
                                                      isPickup ? 'Penjemputan terjadwal' : 'Antar Mandiri',
                                                      style: const TextStyle(
                                                        fontSize: 11,
                                                        color: Color(0xFF64748B),
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 8),
                                          Row(
                                            children: [
                                              const Icon(Icons.location_on_outlined, size: 14, color: Color(0xFF64748B)),
                                              const SizedBox(width: 4),
                                              Expanded(
                                                child: Text(
                                                  isPickup ? (order['pickup_address'] ?? 'Alamat Utama') : 'Diantar ke Outlet L-DRY',
                                                  maxLines: 1,
                                                  overflow: TextOverflow.ellipsis,
                                                  style: const TextStyle(
                                                    fontSize: 11,
                                                    color: Color(0xFF64748B),
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                    ),
                                  );
                                },
                              ),
                            ),
                  const SizedBox(height: 28),

                  // Layanan Kami Section
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Layanan Kami',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0F172A),
                        ),
                      ),
                      GestureDetector(
                        onTap: () {
                          MainNavigationScreen.of(context)?.setTab(1);
                        },
                        child: const Text(
                          'Lihat Semua',
                          style: TextStyle(
                            fontSize: 13,
                            color: Color(0xFF2563EB),
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Layanan Kami Redesigned Layout
                  GestureDetector(
                    onTap: () {
                      AppConfig.preSelectedService = 'Cuci & Setrika';
                      MainNavigationScreen.of(context)?.setTab(1);
                    },
                    child: Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: const Color(0xFF2563EB),
                        borderRadius: BorderRadius.circular(24),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF2563EB).withOpacity(0.15),
                            blurRadius: 12,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Cuci Komplit',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Cuci, Kering, Wangi & Lipat',
                                  style: TextStyle(
                                    color: Colors.white.withOpacity(0.8),
                                    fontSize: 12,
                                  ),
                                ),
                                const SizedBox(height: 16),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(30),
                                  ),
                                  child: const Text(
                                    'Mulai Rp 8.000/kg',
                                    style: TextStyle(
                                      color: Color(0xFF2563EB),
                                      fontSize: 11,
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Icon(
                            Icons.clean_hands_outlined,
                            color: Colors.white.withOpacity(0.2),
                            size: 72,
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: ServiceGridCard(
                          title: 'Setrika Saja',
                          subtitle: 'Cepat & Rapi',
                          icon: Icons.iron_rounded,
                          iconColor: const Color(0xFF9333EA),
                          onTap: () {
                            AppConfig.preSelectedService = 'Setrika Saja';
                            MainNavigationScreen.of(context)?.setTab(1);
                          },
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: ServiceGridCard(
                          title: 'Cuci Satuan',
                          subtitle: 'Jas & Kebaya',
                          icon: Icons.dry_cleaning_rounded,
                          iconColor: const Color(0xFF0D9488),
                          onTap: () {
                            AppConfig.preSelectedService = 'Cuci Satuan';
                            MainNavigationScreen.of(context)?.setTab(1);
                          },
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Offer of the Month coupon clipboard banner
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0F172A),
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.08),
                          blurRadius: 15,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'OFFER OF THE MONTH',
                                style: TextStyle(
                                  color: Color(0xFF3B82F6),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 10,
                                  letterSpacing: 1.0,
                                ),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'Diskon 30%',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w900,
                                  fontSize: 18,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                'Khusus pengguna baru untuk transaksi pertama.',
                                style: TextStyle(
                                  color: Colors.white.withOpacity(0.6),
                                  fontSize: 10,
                                ),
                              ),
                            ],
                          ),
                        ),
                        GestureDetector(
                          onTap: () {
                            Clipboard.setData(const ClipboardData(text: 'LDRY30'));
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text('Kode promo LDRY30 disalin!'),
                                duration: Duration(seconds: 1),
                              ),
                            );
                          },
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.05),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: Colors.white.withOpacity(0.15)),
                            ),
                            child: const Column(
                              children: [
                                Text(
                                  'LDRY30',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 13,
                                    fontFamily: 'monospace',
                                  ),
                                ),
                                SizedBox(height: 2),
                                Text(
                                  'COPY CODE',
                                  style: TextStyle(
                                    color: Color(0xFF2563EB),
                                    fontWeight: FontWeight.bold,
                                    fontSize: 8,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class ServiceGridCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color iconColor;
  final VoidCallback onTap;

  const ServiceGridCard({
    super.key,
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.iconColor,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: const Color(0xFFF1F5F9)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.01),
              blurRadius: 8,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: iconColor.withOpacity(0.08),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: iconColor, size: 24),
            ),
            const SizedBox(height: 16),
            Text(
              title,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: Color(0xFF0F172A),
              ),
            ),
            const SizedBox(height: 2),
            Text(
              subtitle,
              style: const TextStyle(
                fontSize: 11,
                color: Color(0xFF64748B),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ======================== CREATE ORDER SCREEN ========================
class CreateOrderScreen extends StatefulWidget {
  const CreateOrderScreen({super.key});

  @override
  State<CreateOrderScreen> createState() => _CreateOrderScreenState();
}

class _CreateOrderScreenState extends State<CreateOrderScreen> {
  final TextEditingController _addressController = TextEditingController(
    text: AppConfig.userProfile?['address'] ?? '',
  );
  final TextEditingController _notesController = TextEditingController();
  final TextEditingController _weightController = TextEditingController(text: '5');

  String _deliveryMethod = 'pickup';
  List<dynamic> _services = [];
  dynamic _selectedService;
  bool _isLoadingServices = true;
  bool _isSubmitting = false;
  DateTime? _pickupDate;
  String? _pickupTimeSlot;

  final List<String> _timeSlots = [
    '08:00 - 10:00',
    '10:00 - 12:00',
    '12:00 - 14:00',
    '14:00 - 16:00',
    '16:00 - 18:00',
    '18:00 - 20:00'
  ];

  @override
  void initState() {
    super.initState();
    _pickupTimeSlot = _timeSlots[0];
    _fetchServices();
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _checkPreSelectedService();
  }

  void _checkPreSelectedService() {
    if (_services.isNotEmpty && AppConfig.preSelectedService != null) {
      final match = _services.firstWhere(
        (s) => s['name']
            .toString()
            .toLowerCase()
            .contains(AppConfig.preSelectedService!.toLowerCase()),
        orElse: () => null,
      );
      if (match != null) {
        setState(() {
          _selectedService = match;
        });
      }
      AppConfig.preSelectedService = null;
    }
  }

  Future<void> _fetchServices() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/services'),
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        setState(() {
          _services = data['services'];
          if (_services.isNotEmpty) {
            _selectedService = _services[0];
          }
          _isLoadingServices = false;
        });
        _checkPreSelectedService();
      }
    } catch (e) {
      setState(() {
        _isLoadingServices = false;
      });
    }
  }

  Future<void> _selectPickupDate() async {
    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 14)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: Color(0xFF2563EB),
              onPrimary: Colors.white,
              onSurface: Color(0xFF0F172A),
            ),
          ),
          child: child!,
        );
      },
    );

    if (pickedDate != null) {
      setState(() {
        _pickupDate = pickedDate;
      });
    }
  }

  Future<void> _submitOrder() async {
    if (_deliveryMethod == 'pickup' && _pickupDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Jadwal penjemputan tanggal harus diisi!'),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    if (_deliveryMethod == 'pickup' && _addressController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Alamat penjemputan harus diisi!'),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    double weight = double.tryParse(_weightController.text.trim()) ?? 3.0;
    if (weight < 3.0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Estimasi berat minimum adalah 3 kg!'),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      DateTime? combinedDateTime;
      if (_pickupDate != null && _pickupTimeSlot != null) {
        final startHour = int.parse(_pickupTimeSlot!.split(':')[0]);
        final startMin = int.parse(_pickupTimeSlot!.split(':')[1].split(' ')[0]);
        combinedDateTime = DateTime(
          _pickupDate!.year,
          _pickupDate!.month,
          _pickupDate!.day,
          startHour,
          startMin,
        );
      }

      double unitPrice = 0.0;
      if (_selectedService != null) {
        double priceKg = parseDouble(_selectedService['price_per_kg']);
        double pricePcs = parseDouble(_selectedService['price_per_pcs']);
        unitPrice = priceKg > 0 ? priceKg : pricePcs;
      }
      double totalEstimation = unitPrice * weight;

      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/orders'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
        body: jsonEncode({
          'service_id': _selectedService['id'],
          'delivery_method': _deliveryMethod,
          'pickup_address': _addressController.text.trim(),
          'pickup_datetime': combinedDateTime?.toIso8601String(),
          'customer_notes': _notesController.text.trim(),
          'weight_kg': weight,
          'total_price': totalEstimation,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 201 && data['success'] == true) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Berhasil! Nomor Order: ${data['order']['order_number']}',
            ),
            backgroundColor: const Color(0xFF2563EB),
          ),
        );
        _notesController.clear();
        setState(() {
          _pickupDate = null;
          _weightController.text = '5';
        });
        
        // Redirect to History Tab (index 2)
        MainNavigationScreen.of(context)?.setTab(2);
      } else {
        throw Exception(data['message'] ?? 'Gagal membuat pesanan');
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
      );
    } finally {
      setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    double unitPrice = 0.0;
    String serviceUnit = 'kg';
    if (_selectedService != null) {
      double priceKg = parseDouble(_selectedService['price_per_kg']);
      double pricePcs = parseDouble(_selectedService['price_per_pcs']);
      serviceUnit = _selectedService['unit'] ?? 'kg';
      unitPrice = priceKg > 0 ? priceKg : pricePcs;
    }
    double currentWeight = double.tryParse(_weightController.text) ?? 3.0;
    double totalEst = unitPrice * currentWeight;

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Buat Pesanan Baru',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoadingServices
          ? const Center(
              child: CircularProgressIndicator(color: Color(0xFF2563EB)),
            )
          : _services.isEmpty
              ? const Center(
                  child: Padding(
                    padding: EdgeInsets.all(24.0),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.warning_amber_rounded, size: 48, color: Colors.amber),
                        SizedBox(height: 12),
                        Text(
                          'Gagal mengambil data layanan.\nPastikan server aktif dan coba lagi.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Color(0xFF64748B), height: 1.4),
                        ),
                      ],
                    ),
                  ),
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // STEP 1: Pilih Layanan
                      _buildStepTitle('1', 'Pilih Layanan'),
                      const SizedBox(height: 12),
                      Column(
                        children: _services.map((dynamic srv) {
                          final name = srv['name'] ?? 'Layanan';
                          final desc = srv['description'] ?? 'Layanan laundry premium';
                          final unit = srv['unit'] ?? 'kg';
                          final priceKg = parseDouble(srv['price_per_kg']);
                          final pricePcs = parseDouble(srv['price_per_pcs']);
                          int displayPrice = (priceKg > 0 ? priceKg : pricePcs).toInt();
                          bool isSelected = _selectedService?['id'] == srv['id'];

                          IconData serviceIcon = Icons.local_laundry_service_outlined;
                          if (srv['slug'].toString().contains('setrika')) {
                            serviceIcon = Icons.iron_rounded;
                          } else if (srv['slug'].toString().contains('dry')) {
                            serviceIcon = Icons.dry_cleaning_rounded;
                          }

                          return GestureDetector(
                            onTap: () {
                              setState(() {
                                _selectedService = srv;
                              });
                            },
                            child: Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: isSelected ? const Color(0xFF2563EB) : const Color(0xFFE2E8F0),
                                  width: isSelected ? 2 : 1,
                                ),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    padding: const EdgeInsets.all(10),
                                    decoration: BoxDecoration(
                                      color: isSelected
                                          ? const Color(0xFF2563EB).withOpacity(0.1)
                                          : const Color(0xFFF1F5F9),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(
                                      serviceIcon,
                                      color: isSelected ? const Color(0xFF2563EB) : const Color(0xFF64748B),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          name,
                                          style: const TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 14,
                                            color: Color(0xFF0F172A),
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          desc,
                                          style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          'Rp $displayPrice /$unit',
                                          style: const TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 13,
                                            color: Color(0xFF2563EB),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Container(
                                    width: 20,
                                    height: 20,
                                    decoration: BoxDecoration(
                                      shape: BoxShape.circle,
                                      border: Border.all(
                                        color: isSelected ? const Color(0xFF2563EB) : const Color(0xFFCBD5E1),
                                        width: isSelected ? 6 : 2,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                      const SizedBox(height: 20),

                      // STEP 2: Estimasi Berat
                      _buildStepTitle('2', 'Estimasi Berat'),
                      const SizedBox(height: 12),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'MASUKKAN ESTIMASI (KG)',
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF64748B),
                              ),
                            ),
                            const SizedBox(height: 8),
                            Row(
                              children: [
                                Expanded(
                                  child: TextField(
                                    controller: _weightController,
                                    keyboardType: TextInputType.number,
                                    onChanged: (val) {
                                      setState(() {});
                                    },
                                    decoration: InputDecoration(
                                      hintText: 'Contoh: 5',
                                      suffixText: 'kg',
                                      fillColor: const Color(0xFFF8FAFC),
                                      filled: true,
                                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                      border: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(12),
                                        borderSide: BorderSide.none,
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF2563EB).withOpacity(0.08),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: const Text(
                                    'MINIMUM\n3 kg',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(
                                      color: Color(0xFF2563EB),
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              '*Berat aktual akan dihitung oleh Kurir saat penjemputan menggunakan timbangan digital.',
                              style: TextStyle(fontSize: 10, color: Color(0xFF64748B), height: 1.3),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),

                      // STEP 3: Jadwal Penjemputan
                      _buildStepTitle('3', 'Jadwal Penjemputan'),
                      const SizedBox(height: 12),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'PILIH TANGGAL',
                              style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 8),
                            GestureDetector(
                              onTap: _selectPickupDate,
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF8FAFC),
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(color: const Color(0xFFE2E8F0)),
                                ),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      _pickupDate == null
                                          ? 'Pilih Tanggal...'
                                          : '${_pickupDate!.day.toString().padLeft(2, '0')}/${_pickupDate!.month.toString().padLeft(2, '0')}/${_pickupDate!.year}',
                                      style: TextStyle(
                                        color: _pickupDate == null ? Colors.grey : const Color(0xFF0F172A),
                                        fontSize: 13,
                                      ),
                                    ),
                                    const Icon(Icons.calendar_today_outlined, color: Color(0xFF2563EB), size: 18),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 14),
                            const Text(
                              'PILIH JAM',
                              style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 16),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: const Color(0xFFE2E8F0)),
                              ),
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<String>(
                                  value: _pickupTimeSlot,
                                  isExpanded: true,
                                  items: _timeSlots.map((String slot) {
                                    return DropdownMenuItem<String>(
                                      value: slot,
                                      child: Text(slot, style: const TextStyle(fontSize: 13)),
                                    );
                                  }).toList(),
                                  onChanged: (v) {
                                    setState(() {
                                      _pickupTimeSlot = v;
                                    });
                                  },
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),

                      // STEP 4: Alamat Penjemputan
                      _buildStepTitle('4', 'Alamat Penjemputan'),
                      const SizedBox(height: 12),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: const Color(0xFFF8FAFC),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: const Color(0xFFE2E8F0)),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.location_on, color: Color(0xFF2563EB), size: 20),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text(
                                          'Alamat Utama',
                                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          _addressController.text.isNotEmpty
                                              ? _addressController.text
                                              : 'Alamat belum diatur',
                                          style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ],
                                    ),
                                  ),
                                  TextButton(
                                    onPressed: () async {
                                      final refresh = await Navigator.push(
                                        context,
                                        MaterialPageRoute(builder: (context) => const EditProfileScreen()),
                                      );
                                      if (refresh == true) {
                                        setState(() {
                                          _addressController.text = AppConfig.userProfile?['address'] ?? '';
                                        });
                                      }
                                    },
                                    child: const Text('Ganti', style: TextStyle(color: Color(0xFF2563EB), fontSize: 12, fontWeight: FontWeight.bold)),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(height: 14),
                            const Text(
                              'TAMBAH CATATAN UNTUK KURIR (OPSIONAL)',
                              style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: _notesController,
                              maxLines: 2,
                              decoration: InputDecoration(
                                hintText: 'Contoh: Titipkan di lobi atau pagar warna biru',
                                fillColor: const Color(0xFFF8FAFC),
                                filled: true,
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: BorderSide.none,
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // STEP 5: Ringkasan Pesanan
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: const Color(0xFF2563EB).withOpacity(0.04),
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: const Color(0xFF2563EB).withOpacity(0.15)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Ringkasan Pesanan',
                              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                            ),
                            const SizedBox(height: 14),
                            _buildSummaryRow(
                              'Layanan: ${_selectedService?['name'] ?? '-'}',
                              'Rp ${unitPrice.toInt()}',
                            ),
                            _buildSummaryRow(
                              'Estimasi Berat: ${currentWeight.toInt()}$serviceUnit',
                              'x${currentWeight.toInt()}',
                            ),
                            _buildSummaryRow(
                              'Ongkos Kirim',
                              'GRATIS',
                              valueColor: Colors.green,
                              isBoldValue: true,
                            ),
                            const Divider(height: 24, color: Color(0xFFE2E8F0)),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                const Text(
                                  'TOTAL ESTIMASI',
                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                ),
                                Text(
                                  'Rp ${_formatPrice(totalEst)}',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w900,
                                    fontSize: 18,
                                    color: Color(0xFF2563EB),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Submit Button
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isSubmitting ? null : _submitOrder,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF2563EB),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 18),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            elevation: 0,
                          ),
                          child: _isSubmitting
                              ? const SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                                )
                              : const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text('Pesan Sekarang', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 15)),
                                    SizedBox(width: 8),
                                    Icon(Icons.arrow_forward, size: 16),
                                  ],
                                ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.shield_outlined, size: 14, color: Colors.grey.shade500),
                          const SizedBox(width: 4),
                          Text(
                            'Garansi pakaian bersih & aman. S&K Berlaku.',
                            style: TextStyle(color: Colors.grey.shade500, fontSize: 10),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),

                      // L-DRY Premium Promo Banner
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          borderRadius: BorderRadius.circular(24),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'L-DRY Premium',
                                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    'Nikmati pengiriman ekspres & loyalitas poin lebih besar.',
                                    style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 10, height: 1.3),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 12),
                            ElevatedButton(
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (context) => const RewardsScreen()),
                                );
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.white.withOpacity(0.08),
                                foregroundColor: Colors.white,
                                elevation: 0,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  side: BorderSide(color: Colors.white.withOpacity(0.15)),
                                ),
                                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                              ),
                              child: const Text('Lihat Member', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],
                  ),
                ),
    );
  }

  Widget _buildStepTitle(String number, String title) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: const Color(0xFF2563EB),
            borderRadius: BorderRadius.circular(6),
          ),
          child: Text(
            number,
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 11),
          ),
        ),
        const SizedBox(width: 10),
        Text(
          title,
          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
        ),
      ],
    );
  }

  Widget _buildSummaryRow(String label, String value, {Color? valueColor, bool isBoldValue = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
          Text(
            value,
            style: TextStyle(
              fontSize: 12,
              fontWeight: isBoldValue ? FontWeight.bold : FontWeight.normal,
              color: valueColor ?? const Color(0xFF0F172A),
            ),
          ),
        ],
      ),
    );
  }

  String _formatPrice(double val) {
    final RegExp reg = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    return val.toInt().toString().replaceAllMapped(reg, (Match match) => '${match[1]}.');
  }
}




// ======================== HISTORY SCREEN ========================
class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  List _orders = [];
  bool _isLoading = true;
  String _selectedTab = 'Semua'; // Semua, Aktif, Selesai
  String _searchQuery = '';
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchOrders();
  }

  Future<void> _fetchOrders() async {
    if (mounted) setState(() => _isLoading = true);
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/orders'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        setState(() {
          _orders = data['orders'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal mengambil data: $e'),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    }
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'waiting':
        return const Color(0xFF64748B);
      case 'confirmed':
        return const Color(0xFF2563EB);
      case 'picked_up':
        return const Color(0xFF4F46E5);
      case 'washing':
        return const Color(0xFF9333EA);
      case 'done':
        return const Color(0xFF0D9488);
      case 'ready':
        return const Color(0xFF059669);
      case 'delivered':
        return const Color(0xFF16A34A);
      default:
        return const Color(0xFF64748B);
    }
  }

  @override
  Widget build(BuildContext context) {
    int countAll = _orders.length;
    int countActive = _orders.where((o) => o['status'] != 'delivered' && o['status'] != 'cancelled').length;
    int countSelesai = _orders.where((o) => o['status'] == 'delivered' || o['status'] == 'cancelled').length;

    List<dynamic> filteredOrders = _orders.where((order) {
      final q = _searchQuery.toLowerCase();
      final orderNo = order['order_number']?.toString().toLowerCase() ?? '';
      final serviceName = order['service_name']?.toString().toLowerCase() ?? '';
      final matchesSearch = orderNo.contains(q) || serviceName.contains(q);
      if (!matchesSearch) return false;

      if (_selectedTab == 'Aktif') {
        return order['status'] != 'delivered' && order['status'] != 'cancelled';
      } else if (_selectedTab == 'Selesai') {
        return order['status'] == 'delivered' || order['status'] == 'cancelled';
      }
      return true;
    }).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Riwayat Pesanan',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Color(0xFF2563EB)),
            onPressed: _fetchOrders,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: Color(0xFF2563EB)),
            )
          : RefreshIndicator(
              color: const Color(0xFF2563EB),
              onRefresh: _fetchOrders,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: TextField(
                              controller: _searchController,
                              onChanged: (val) {
                                setState(() {
                                  _searchQuery = val;
                                });
                              },
                              decoration: const InputDecoration(
                                prefixIcon: Icon(Icons.search, color: Color(0xFF64748B)),
                                hintText: 'Cari ID Pesanan atau Layanan...',
                                hintStyle: TextStyle(fontSize: 13, color: Color(0xFF94A3B8)),
                                border: InputBorder.none,
                                contentPadding: EdgeInsets.symmetric(vertical: 14),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                          ),
                          child: const Row(
                            children: [
                              Icon(Icons.filter_list, color: Color(0xFF2563EB), size: 20),
                              SizedBox(width: 4),
                              Text('Filter', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF2563EB))),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 18),

                    Row(
                      children: [
                        _buildTabButton('Semua', countAll),
                        const SizedBox(width: 8),
                        _buildTabButton('Aktif', countActive),
                        const SizedBox(width: 8),
                        _buildTabButton('Selesai', countSelesai),
                      ],
                    ),
                    const SizedBox(height: 20),

                    if ((_selectedTab == 'Semua' || _selectedTab == 'Aktif') && _searchQuery.isEmpty)
                      ...filteredOrders
                          .where((o) => o['status'] != 'delivered' && o['status'] != 'cancelled')
                          .map((order) {
                        return _buildActiveOrderCard(order);
                      }),

                    Container(
                      width: double.infinity,
                      margin: const EdgeInsets.only(bottom: 24),
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(24),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Total Hemat', style: TextStyle(color: Color(0xFF3B82F6), fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1.0)),
                          const SizedBox(height: 4),
                          const Text('128 Jam', style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900)),
                          const SizedBox(height: 4),
                          Text(
                            'Waktu yang Anda hemat dengan L-DRY bulan ini.',
                            style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 11),
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Text('Lihat Insight Laundry', style: TextStyle(color: Colors.white.withOpacity(0.9), fontSize: 11, fontWeight: FontWeight.bold)),
                              const SizedBox(width: 6),
                              Icon(Icons.arrow_forward, color: Colors.white.withOpacity(0.9), size: 14),
                            ],
                          ),
                        ],
                      ),
                    ),

                    const Text(
                      'Pesanan Sebelumnya',
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 14),

                    filteredOrders.isEmpty
                        ? const Center(
                            child: Padding(
                              padding: EdgeInsets.all(24.0),
                              child: Text('Tidak ada riwayat pesanan', style: TextStyle(color: Color(0xFF64748B))),
                            ),
                          )
                        : ListView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: filteredOrders.length,
                            itemBuilder: (context, index) {
                              final order = filteredOrders[index];
                              final isCompleted = order['status'] == 'delivered' || order['status'] == 'cancelled';
                              if (!isCompleted && (_selectedTab == 'Semua' || _selectedTab == 'Aktif') && _searchQuery.isEmpty) {
                                  return const SizedBox.shrink();
                              }

                              final double priceNum = parseDouble(order['total_price']);
                              final statusColor = _getStatusColor(order['status']);

                              return GestureDetector(
                                onTap: () async {
                                  final refresh = await Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => OrderDetailScreen(orderId: order['id']),
                                    ),
                                  );
                                  if (refresh == true) {
                                    _fetchOrders();
                                  }
                                },
                                child: Container(
                                  margin: const EdgeInsets.only(bottom: 12),
                                  padding: const EdgeInsets.all(16),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(color: const Color(0xFFF1F5F9)),
                                  ),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(10),
                                        decoration: BoxDecoration(
                                          color: statusColor.withOpacity(0.08),
                                          shape: BoxShape.circle,
                                        ),
                                        child: Icon(
                                          Icons.assignment_turned_in_outlined,
                                          color: statusColor,
                                        ),
                                      ),
                                      const SizedBox(width: 14),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              '#${order['order_number']}',
                                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                            ),
                                            const SizedBox(height: 2),
                                            Text(
                                              '${order['created_at']} — ${order['service_name'] ?? 'Laundry'}',
                                              style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                            ),
                                            const SizedBox(height: 6),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                              decoration: BoxDecoration(
                                                color: statusColor.withOpacity(0.08),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                order['status_label']?.toString().toUpperCase() ?? 'SELESAI',
                                                style: TextStyle(color: statusColor, fontSize: 9, fontWeight: FontWeight.bold),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          Text(
                                            priceNum > 0 ? 'Rp ${_formatPrice(priceNum)}' : 'Rp 0',
                                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                          ),
                                          const SizedBox(height: 4),
                                          const Icon(Icons.arrow_forward_ios, size: 12, color: Color(0xFF94A3B8)),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildTabButton(String tab, int count) {
    bool isSelected = _selectedTab == tab;
    String label = tab;
    if (tab != 'Semua') {
      label = '$tab ($count)';
    }

    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            _selectedTab = tab;
          });
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: isSelected ? const Color(0xFF2563EB) : Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: isSelected ? const Color(0xFF2563EB) : const Color(0xFFE2E8F0)),
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: isSelected ? Colors.white : const Color(0xFF64748B),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildActiveOrderCard(dynamic order) {
    final status = order['status']?.toString() ?? '';
    final double priceNum = parseDouble(order['total_price']);

    return Container(
      margin: const EdgeInsets.only(bottom: 24),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFF2563EB).withOpacity(0.25), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF2563EB).withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(Icons.change_circle_outlined, color: Color(0xFF2563EB), size: 18),
                  const SizedBox(width: 8),
                  Text(
                    '#${order['order_number']}',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFF2563EB).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  order['status_label']?.toString().toUpperCase() ?? 'PROSES',
                  style: const TextStyle(color: Color(0xFF2563EB), fontSize: 10, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Text(
            '${order['service_name'] ?? 'Laundry'} - ${order['weight_kg'] ?? '0'} ${order['service_unit'] ?? 'kg'}',
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
          ),
          const SizedBox(height: 4),
          Text(
            'Order: ${order['created_at']} | Estimasi: ${order['pickup_datetime'] ?? '-'}',
            style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
          ),
          if (priceNum > 0) ...[
            const SizedBox(height: 6),
            Text(
              'Rp ${_formatPrice(priceNum)}',
              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF2563EB)),
            ),
          ],
          const SizedBox(height: 14),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () async {
                final refresh = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => OrderDetailScreen(orderId: order['id'])),
                );
                if (refresh == true) {
                  _fetchOrders();
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF2563EB),
                foregroundColor: Colors.white,
                elevation: 0,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('Lihat Detail', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
            ),
          ),
          const Divider(height: 24, color: Color(0xFFF1F5F9)),
          _buildStepperTimeline(status),
        ],
      ),
    );
  }

  Widget _buildStepperTimeline(String status) {
    bool step1 = false; // dijemput
    bool step2 = false; // dicuci
    bool step3 = false; // dikirim

    if (status == 'picked_up' || status == 'washing' || status == 'done' || status == 'ready' || status == 'delivered') {
      step1 = true;
    }
    if (status == 'washing' || status == 'done' || status == 'ready' || status == 'delivered') {
      step2 = true;
    }
    if (status == 'ready' || status == 'delivered') {
      step3 = true;
    }

    return Row(
      children: [
        _buildStepDot('DIJEMPUT', step1),
        Expanded(child: Container(height: 2, color: step2 ? const Color(0xFF2563EB) : const Color(0xFFE2E8F0))),
        _buildStepDot('DICUCI', step2),
        Expanded(child: Container(height: 2, color: step3 ? const Color(0xFF2563EB) : const Color(0xFFE2E8F0))),
        _buildStepDot('DIKIRIM', step3),
      ],
    );
  }

  Widget _buildStepDot(String label, bool active) {
    return Column(
      children: [
        Container(
          width: 24,
          height: 24,
          decoration: BoxDecoration(
            color: active ? const Color(0xFF2563EB) : Colors.white,
            shape: BoxShape.circle,
            border: Border.all(color: active ? const Color(0xFF2563EB) : const Color(0xFFCBD5E1), width: 2),
          ),
          child: active ? const Icon(Icons.check, color: Colors.white, size: 12) : null,
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(
            fontSize: 9,
            fontWeight: FontWeight.bold,
            color: active ? const Color(0xFF2563EB) : const Color(0xFF94A3B8),
          ),
        ),
      ],
    );
  }

  String _formatPrice(double val) {
    final RegExp reg = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    return val.toInt().toString().replaceAllMapped(reg, (Match match) => '${match[1]}.');
  }
}

// ======================== PROFILE SCREEN ========================
class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  bool _isLoading = false;
  int _points = 0;
  int _activeOrdersCount = 0;

  @override
  void initState() {
    super.initState();
    _refreshProfile();
  }

  Future<void> _refreshProfile() async {
    if (AppConfig.token == null) return;
    if (mounted) setState(() => _isLoading = true);

    try {
      // 1. Refresh user profile
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/user'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          setState(() {
            AppConfig.userProfile = data['user'];
            _points = parseInt(AppConfig.userProfile?['points']);
          });
        }
      }

      // 2. Refresh active orders count
      final ordersRes = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/orders'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (ordersRes.statusCode == 200) {
        final ordersData = jsonDecode(ordersRes.body);
        if (ordersData['success'] == true) {
          final List ordersList = ordersData['orders'] ?? [];
          final activeList = ordersList.where((o) => o['status'] != 'delivered' && o['status'] != 'cancelled').toList();
          setState(() {
            _activeOrdersCount = activeList.length;
          });
        }
      }
    } catch (e) {
      // catch silently
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  String _formatPoints(int pts) {
    final RegExp reg = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    return pts.toString().replaceAllMapped(reg, (Match match) => '${match[1]},');
  }

  @override
  Widget build(BuildContext context) {
    final name = AppConfig.userProfile?['name'] ?? 'Pelanggan';
    final email = AppConfig.userProfile?['email'] ?? '';
    final phone = AppConfig.userProfile?['phone'] ?? '';

    return Scaffold(
      body: SafeArea(
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: Color(0xFF2563EB)))
            : RefreshIndicator(
                color: const Color(0xFF2563EB),
                onRefresh: _refreshProfile,
                child: SingleChildScrollView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      // Header Top bar: L-DRY and Notification Bell
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Image.network(
                            'https://l-dry.my.id/logo.png',
                            height: 28,
                            fit: BoxFit.contain,
                            errorBuilder: (context, error, stackTrace) => const Row(
                              children: [
                                Icon(Icons.local_laundry_service_rounded, color: Color(0xFF2563EB), size: 24),
                                SizedBox(width: 8),
                                Text(
                                  'L-DRY',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.w900,
                                    color: Color(0xFF1E3A8A),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          IconButton(
                            icon: const Icon(
                              Icons.notifications_none_rounded,
                              color: Color(0xFF0F172A),
                              size: 26,
                            ),
                            onPressed: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => const NotificationListScreen(),
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Avatar, Name, Tier details card
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(28),
                          border: Border.all(color: const Color(0xFFF1F5F9)),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.01),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            Stack(
                              children: [
                                CircleAvatar(
                                  radius: 44,
                                  backgroundColor: const Color(0xFF2563EB).withOpacity(0.1),
                                  child: Text(
                                    name.substring(0, name.length >= 2 ? 2 : name.length).toUpperCase(),
                                    style: const TextStyle(
                                      color: Color(0xFF2563EB),
                                      fontSize: 28,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                Positioned(
                                  right: 0,
                                  bottom: 0,
                                  child: GestureDetector(
                                    onTap: () async {
                                      final refresh = await Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (context) => const EditProfileScreen(),
                                        ),
                                      );
                                      if (refresh == true) {
                                        _refreshProfile();
                                      }
                                    },
                                    child: Container(
                                      padding: const EdgeInsets.all(6),
                                      decoration: const BoxDecoration(
                                        color: Color(0xFF2563EB),
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(Icons.edit_outlined, size: 14, color: Colors.white),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                              decoration: BoxDecoration(
                                color: const Color(0xFF2563EB).withOpacity(0.1),
                                borderRadius: BorderRadius.circular(30),
                              ),
                              child: const Text(
                                'PREMIUM MEMBER',
                                style: TextStyle(
                                  color: Color(0xFF2563EB),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 10,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              name,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF0F172A),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              phone.isNotEmpty ? phone : '-',
                              style: const TextStyle(color: Color(0xFF64748B), fontSize: 13),
                            ),
                            Text(
                              email,
                              style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Loyalty Points Banner Card
                      GestureDetector(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const RewardsScreen(),
                            ),
                          ).then((_) => _refreshProfile());
                        },
                        child: Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            color: const Color(0xFF2563EB),
                            borderRadius: BorderRadius.circular(24),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF2563EB).withOpacity(0.15),
                                blurRadius: 15,
                                offset: const Offset(0, 6),
                              ),
                            ],
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'TOTAL POIN LOYALITAS',
                                    style: TextStyle(
                                      color: Colors.white.withOpacity(0.8),
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    '${_formatPoints(_points)} pts',
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 28,
                                      fontWeight: FontWeight.w900,
                                    ),
                                  ),
                                  const SizedBox(height: 6),
                                  Text(
                                    'Dapat ditukarkan dengan 2x Cuci Gratis',
                                    style: TextStyle(
                                      color: Colors.white.withOpacity(0.9),
                                      fontSize: 11,
                                    ),
                                  ),
                                ],
                              ),
                              const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 20),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Active Orders Status Card
                      Container(
                        padding: const EdgeInsets.all(18),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: const Color(0xFFF1F5F9)),
                        ),
                        child: Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: const Color(0xFF2563EB).withOpacity(0.08),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              child: const Icon(Icons.local_shipping_outlined, color: Color(0xFF2563EB), size: 22),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'Pesanan Aktif',
                                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    '$_activeOrdersCount pesanan sedang diproses',
                                    style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
                                  ),
                                ],
                              ),
                            ),
                            ElevatedButton(
                              onPressed: () {
                                MainNavigationScreen.of(context)?.setTab(2);
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFFF8FAFC),
                                foregroundColor: const Color(0xFF2563EB),
                                surfaceTintColor: Colors.transparent,
                                elevation: 0,
                                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                  side: const BorderSide(color: Color(0xFFE2E8F0)),
                                ),
                              ),
                              child: const Text('Lacak', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Profile Menu List Items
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: const Color(0xFFF1F5F9)),
                        ),
                        child: Column(
                          children: [
                            _buildProfileMenuItem(
                              Icons.location_on_outlined,
                              'Alamat Saya',
                              'Kelola lokasi jemput & antar',
                              onTap: () async {
                                final refresh = await Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => const EditProfileScreen(),
                                  ),
                                );
                                if (refresh == true) {
                                  _refreshProfile();
                                }
                              },
                            ),
                            _buildProfileMenuItem(
                              Icons.payment_outlined,
                              'Metode Pembayaran',
                              'Kartu, E-wallet, & Transfer Bank',
                              onTap: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Metode Pembayaran dapat dipilih saat melakukan pembayaran.')),
                                );
                              },
                            ),
                            _buildProfileMenuItem(
                              Icons.confirmation_number_outlined,
                              'Voucher & Promo',
                              'Lihat dan tukarkan poin Anda',
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => const RewardsScreen(),
                                  ),
                                ).then((_) => _refreshProfile());
                              },
                              showBadge: true,
                            ),
                            _buildProfileMenuItem(
                              Icons.chat_bubble_outline,
                              'Hubungi Kami',
                              'Bantuan & Layanan Pelanggan',
                              onTap: () async {
                                final uri = Uri.parse('https://wa.me/6282332855157?text=Halo%20L-DRY%20Laundry%20Surabaya');
                                try {
                                  await launchUrl(uri, mode: LaunchMode.externalApplication);
                                } catch (e) {
                                  if (mounted) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('Gagal membuka WhatsApp')),
                                    );
                                  }
                                }
                              },
                            ),
                            _buildProfileMenuItem(
                              Icons.logout_rounded,
                              'Keluar',
                              'Logout dari sesi Anda',
                              textColor: Colors.redAccent,
                              iconColor: Colors.redAccent,
                              onTap: () {
                                AppConfig.token = null;
                                AppConfig.userProfile = null;
                                Navigator.pushAndRemoveUntil(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => const LoginScreen(),
                                  ),
                                  (route) => false,
                                );
                              },
                              isLast: true,
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Referral Banner Card at Bottom
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: const Color(0xFF0F172A),
                          borderRadius: BorderRadius.circular(24),
                        ),
                        child: Column(
                          children: [
                            const Text(
                              'Ajak Teman, Dapat Poin!',
                              style: TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w900,
                                fontSize: 16,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              'Berikan diskon Rp20rb ke temanmu dan dapatkan 500 poin saat mereka selesai mencuci.',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.6),
                                fontSize: 11,
                                height: 1.4,
                              ),
                            ),
                            const SizedBox(height: 16),
                            ElevatedButton(
                              onPressed: () {
                                Clipboard.setData(const ClipboardData(text: 'LDRYREFERRAL'));
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Kode referral disalin!')),
                                );
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.white,
                                foregroundColor: const Color(0xFF0F172A),
                                elevation: 0,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                              ),
                              child: const Text('Bagikan Kode', style: TextStyle(fontWeight: FontWeight.bold)),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 32),
                    ],
                  ),
                ),
              ),
      ),
    );
  }

  Widget _buildProfileMenuItem(
    IconData icon,
    String title,
    String subtitle, {
    required VoidCallback onTap,
    Color textColor = const Color(0xFF0F172A),
    Color iconColor = const Color(0xFF2563EB),
    bool isLast = false,
    bool showBadge = false,
  }) {
    return Column(
      children: [
        ListTile(
          onTap: onTap,
          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: iconColor.withOpacity(0.08),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: iconColor, size: 20),
          ),
          title: Text(
            title,
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 14,
              color: textColor,
            ),
          ),
          subtitle: Text(
            subtitle,
            style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
          ),
          trailing: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              if (showBadge)
                Container(
                  width: 8,
                  height: 8,
                  margin: const EdgeInsets.only(right: 12),
                  decoration: const BoxDecoration(
                    color: Colors.redAccent,
                    shape: BoxShape.circle,
                  ),
                ),
              const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Color(0xFF64748B)),
            ],
          ),
        ),
        if (!isLast)
          const Divider(
            height: 1,
            color: Color(0xFFF1F5F9),
            indent: 20,
            endIndent: 20,
          ),
      ],
    );
  }
}

// ======================== EDIT PROFILE SCREEN ========================
class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nameController;
  late final TextEditingController _phoneController;
  late final TextEditingController _addressController;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: AppConfig.userProfile?['name'] ?? '');
    _phoneController = TextEditingController(text: AppConfig.userProfile?['phone'] ?? '');
    _addressController = TextEditingController(text: AppConfig.userProfile?['address'] ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSaving = true);

    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/user/update'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
        body: jsonEncode({
          'name': _nameController.text.trim(),
          'phone': _phoneController.text.trim(),
          'address': _addressController.text.trim(),
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        AppConfig.userProfile = data['user'];
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Profil berhasil diperbarui!'),
              backgroundColor: Color(0xFF2563EB),
            ),
          );
          Navigator.pop(context, true);
        }
      } else {
        throw Exception(data['message'] ?? 'Gagal memperbarui profil');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
        );
      }
    } finally {
      setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Edit Profil', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'NAMA LENGKAP',
                style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _nameController,
                validator: (value) => value == null || value.trim().isEmpty ? 'Nama lengkap harus diisi' : null,
                decoration: InputDecoration(
                  fillColor: Colors.white,
                  filled: true,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),
              const Text(
                'NOMOR WHATSAPP',
                style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _phoneController,
                validator: (value) => value == null || value.trim().isEmpty ? 'Nomor WhatsApp harus diisi' : null,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  fillColor: Colors.white,
                  filled: true,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 18),
              const Text(
                'ALAMAT LENGKAP',
                style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _addressController,
                validator: (value) => value == null || value.trim().isEmpty ? 'Alamat lengkap harus diisi' : null,
                maxLines: 3,
                decoration: InputDecoration(
                  fillColor: Colors.white,
                  filled: true,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _saveProfile,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563EB),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
                  ),
                  child: _isSaving
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('Simpan Perubahan', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ======================== ORDER DETAIL SCREEN ========================
class OrderDetailScreen extends StatefulWidget {
  final int orderId;
  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  dynamic _order;
  bool _isLoading = true;
  bool _isSavingReschedule = false;

  @override
  void initState() {
    super.initState();
    _fetchOrderDetails();
  }

  Future<void> _fetchOrderDetails() async {
    setState(() => _isLoading = true);
    try {
      print('DEBUG: Fetching order details from ${AppConfig.baseUrl}/api/orders/${widget.orderId}');
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/orders/${widget.orderId}'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      print('DEBUG: Order details status code: ${response.statusCode}');
      print('DEBUG: Order details response body: ${response.body}');
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        setState(() {
          _order = data['order'];
          _isLoading = false;
        });
      } else {
        throw Exception(data['message'] ?? 'Gagal mengambil detail pesanan');
      }
    } catch (e) {
      print('DEBUG: Exception in _fetchOrderDetails: $e');
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
        );
      }
    }
  }

  Future<void> _payOrder() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/orders/${widget.orderId}/payment-token'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        final snapToken = data['snap_token'];
        final redirectUrl = data['redirect_url']?.toString();
        if (!mounted) return;
        final paid = await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MidtransPaymentScreen(
              snapToken: snapToken,
              redirectUrl: redirectUrl,
            ),
          ),
        );
        if (paid == true) {
          _fetchOrderDetails();
        }
      } else {
        throw Exception(data['message'] ?? 'Gagal memproses pembayaran');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
        );
      }
    }
  }

  Future<void> _reschedulePickup() async {
    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 14)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: Color(0xFF2563EB),
              onPrimary: Colors.white,
              onSurface: Color(0xFF0F172A),
            ),
          ),
          child: child!,
        );
      },
    );

    if (pickedDate != null) {
      if (!mounted) return;
      final TimeOfDay? pickedTime = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
        builder: (context, child) {
          return Theme(
            data: Theme.of(context).copyWith(
              colorScheme: const ColorScheme.light(
                primary: Color(0xFF2563EB),
                onPrimary: Colors.white,
                onSurface: Color(0xFF0F172A),
              ),
            ),
            child: child!,
          );
        },
      );

      if (pickedTime != null) {
        final newDateTime = DateTime(
          pickedDate.year,
          pickedDate.month,
          pickedDate.day,
          pickedTime.hour,
          pickedTime.minute,
        );

        setState(() => _isSavingReschedule = true);
        try {
          final response = await http.post(
            Uri.parse('${AppConfig.baseUrl}/api/orders/${widget.orderId}/reschedule'),
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer ${AppConfig.token}',
            },
            body: jsonEncode({
              'pickup_datetime': newDateTime.toIso8601String(),
            }),
          );

          final data = jsonDecode(response.body);
          if (response.statusCode == 200 && data['success'] == true) {
            if (mounted) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Jadwal penjemputan berhasil diubah!'),
                  backgroundColor: Color(0xFF2563EB),
                ),
              );
            }
            _fetchOrderDetails();
          } else {
            throw Exception(data['message'] ?? 'Gagal mengubah jadwal');
          }
        } catch (e) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
            );
          }
        } finally {
          setState(() => _isSavingReschedule = false);
        }
      }
    }
  }

  Future<void> _chatAdmin() async {
    final url = _order?['whatsapp_url'];
    if (url != null) {
      final uri = Uri.parse(url);
      try {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Gagal membuka WhatsApp')),
          );
        }
      }
    }
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'waiting':
        return const Color(0xFF64748B);
      case 'confirmed':
        return const Color(0xFF2563EB);
      case 'picked_up':
        return const Color(0xFF4F46E5);
      case 'washing':
        return const Color(0xFF9333EA);
      case 'done':
        return const Color(0xFF0D9488);
      case 'ready':
        return const Color(0xFF059669);
      case 'delivered':
        return const Color(0xFF16A34A);
      default:
        return const Color(0xFF64748B);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          _order != null ? 'Detail ${_order['order_number']}' : 'Detail Pesanan',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context, true),
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: Color(0xFF2563EB)),
            )
          : _order == null
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24.0),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.error_outline_rounded,
                          size: 72,
                          color: Colors.red.shade400,
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Gagal Memuat Detail Pesanan',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'Terjadi kesalahan saat mengambil data dari server. Silakan coba beberapa saat lagi.',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 14,
                            color: Color(0xFF64748B),
                          ),
                        ),
                        const SizedBox(height: 24),
                        SizedBox(
                          width: 200,
                          child: ElevatedButton.icon(
                            icon: const Icon(Icons.refresh_rounded),
                            label: const Text('Coba Lagi'),
                            onPressed: _fetchOrderDetails,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF2563EB),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Status summary card
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: const Color(0xFFF1F5F9)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              _order['service_name'] ?? 'Layanan',
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF0F172A),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: _getStatusColor(_order['status']).withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                _order['status_label'] ?? '',
                                style: TextStyle(
                                  color: _getStatusColor(_order['status']),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 12,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const Divider(height: 30, color: Color(0xFFF1F5F9)),
                        _buildDetailRow('Metode Pengantaran', _order['delivery_method'] == 'pickup' ? 'Kurir Jemput' : 'Antar Sendiri'),
                        if (_order['delivery_method'] == 'pickup') ...[
                          _buildDetailRow('Jadwal Jemput', _order['pickup_datetime'] ?? '-'),
                          _buildDetailRow('Alamat Jemput', _order['pickup_address'] ?? '-'),
                        ],
                        _buildDetailRow('Berat Cucian', _order['weight_kg'] != null ? '${_order['weight_kg']} ${_order['service_unit']}' : 'Menunggu kurir'),
                        _buildDetailRow('Estimasi Selesai', _order['estimated_finish'] ?? '-'),
                        _buildDetailRow(
                          'Status Pembayaran',
                          _order['payment_status_label'] ?? 'Belum Dibayar',
                          valueColor: _order['payment_status'] == 'settlement' ? Colors.green : Colors.amber.shade800,
                        ),
                        _buildDetailRow(
                          'Total Biaya',
                          _order['total_price'] != null ? 'Rp ${parseInt(_order['total_price'])}' : 'Menunggu kurir',
                          valueColor: const Color(0xFF2563EB),
                          isBold: true,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Action Buttons
                  if (_order['total_price'] != null && _order['payment_status'] != 'settlement')
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.payment_outlined),
                        label: const Text('Bayar Sekarang'),
                        onPressed: _payOrder,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          elevation: 0,
                        ),
                      ),
                    ),
                  if (_order['delivery_method'] == 'pickup' &&
                      (_order['status'] == 'waiting' || _order['status'] == 'confirmed')) ...[
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        icon: const Icon(Icons.edit_calendar_outlined),
                        label: _isSavingReschedule
                            ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF2563EB)),
                              )
                            : const Text('Ubah Jadwal Jemput'),
                        onPressed: _isSavingReschedule ? null : _reschedulePickup,
                        style: OutlinedButton.styleFrom(
                          side: const BorderSide(color: Color(0xFF2563EB)),
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                      ),
                    ),
                  ],
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      icon: const Icon(Icons.chat_bubble_outline),
                      label: const Text('Tanya Admin via WhatsApp'),
                      onPressed: _chatAdmin,
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: Color(0xFF64748B)),
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 30),

                  // Tracking timeline title
                  const Text(
                    'Riwayat Status Cucian',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Timeline Logs
                  if (_order['logs'] != null)
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: (_order['logs'] as List).length,
                      itemBuilder: (context, index) {
                        final log = _order['logs'][index];
                        final isLast = index == (_order['logs'] as List).length - 1;
                        return Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Column(
                              children: [
                                Container(
                                  width: 16,
                                  height: 16,
                                  decoration: BoxDecoration(
                                    color: index == 0 ? const Color(0xFF2563EB) : const Color(0xFFE2E8F0),
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                      color: index == 0 ? const Color(0xFF2563EB).withValues(alpha: 0.2) : Colors.transparent,
                                      width: 4,
                                    ),
                                  ),
                                ),
                                if (!isLast)
                                  Container(
                                    width: 2,
                                    height: 50,
                                    color: const Color(0xFFE2E8F0),
                                  ),
                              ],
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        log['status_label'] ?? '',
                                        style: TextStyle(
                                          fontWeight: FontWeight.bold,
                                          fontSize: 14,
                                          color: index == 0 ? const Color(0xFF0F172A) : const Color(0xFF64748B),
                                        ),
                                      ),
                                      Text(
                                        log['created_at'] ?? '',
                                        style: const TextStyle(
                                          fontSize: 11,
                                          color: Color(0xFF64748B),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    log['note'] ?? '',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Color(0xFF64748B),
                                    ),
                                  ),
                                  const SizedBox(height: 16),
                                ],
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                ],
              ),
            ),
    );
  }

  Widget _buildDetailRow(String label, String value, {Color? valueColor, bool isBold = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(color: Color(0xFF64748B), fontSize: 13),
          ),
          Flexible(
            child: Text(
              value,
              textAlign: TextAlign.right,
              style: TextStyle(
                fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
                color: valueColor ?? const Color(0xFF0F172A),
                fontSize: 13,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ======================== MIDTRANS PAYMENT SCREEN ========================
class MidtransPaymentScreen extends StatefulWidget {
  final String snapToken;
  final String? redirectUrl;
  const MidtransPaymentScreen({super.key, required this.snapToken, this.redirectUrl});

  @override
  State<MidtransPaymentScreen> createState() => _MidtransPaymentScreenState();
}

class _MidtransPaymentScreenState extends State<MidtransPaymentScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    String paymentUrl = widget.redirectUrl ?? (AppConfig.baseUrl.contains('l-dry.my.id')
        ? 'https://app.midtrans.com/snap/v2/vtweb/${widget.snapToken}'
        : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/${widget.snapToken}');

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (url) => setState(() => _isLoading = true),
          onPageFinished: (url) {
            setState(() => _isLoading = false);
            if (url.contains('pdf-instruction') || url.contains('finish') || url.contains('callback')) {
              Future.delayed(const Duration(seconds: 2), () {
                if (mounted) Navigator.pop(context, true);
              });
            }
          },
          onNavigationRequest: (request) {
            if (request.url.startsWith('whatsapp://') || request.url.startsWith('intent://')) {
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(paymentUrl));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pembayaran L-DRY', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: CircularProgressIndicator(color: Color(0xFF2563EB)),
            ),
        ],
      ),
    );
  }
}

// ======================== NOTIFICATION LIST SCREEN ========================
class NotificationListScreen extends StatefulWidget {
  const NotificationListScreen({super.key});

  @override
  State<NotificationListScreen> createState() => _NotificationListScreenState();
}

class _NotificationListScreenState extends State<NotificationListScreen> {
  List<dynamic> _notifications = [];
  int _unreadCount = 0;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchNotifications();
  }

  Future<void> _fetchNotifications() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/notifications'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      setState(() {
        _notifications = data['notifications'] ?? [];
        _unreadCount = data['unread_count'] ?? 0;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _markAsRead(int id) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/notifications/$id/read'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        _fetchNotifications();
      }
    } catch (e) {
      // silent fail
    }
  }

  Future<void> _markAllAsRead() async {
    setState(() => _isLoading = true);
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/notifications/read-all'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        _fetchNotifications();
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
        actions: [
          if (_unreadCount > 0)
            TextButton(
              onPressed: _markAllAsRead,
              child: const Text(
                'Tandai Semua Dibaca',
                style: TextStyle(color: Color(0xFF2563EB), fontWeight: FontWeight.bold, fontSize: 12),
              ),
            ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF2563EB)))
          : _notifications.isEmpty
              ? const Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.notifications_off_outlined, size: 48, color: Color(0xFF94A3B8)),
                      SizedBox(height: 12),
                      Text('Belum ada notifikasi', style: TextStyle(color: Color(0xFF64748B))),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _fetchNotifications,
                  color: const Color(0xFF2563EB),
                  child: ListView.builder(
                    itemCount: _notifications.length,
                    itemBuilder: (context, index) {
                      final n = _notifications[index];
                      final bool isRead = n['is_read'] == true;
                      return Container(
                        decoration: BoxDecoration(
                          color: isRead ? Colors.transparent : const Color(0xFF2563EB).withOpacity(0.04),
                          border: const Border(bottom: BorderSide(color: Color(0xFFF1F5F9))),
                        ),
                        child: ListTile(
                          onTap: () {
                            if (!isRead) {
                              _markAsRead(n['id']);
                            }
                            if (n['order_id'] != null) {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => OrderDetailScreen(orderId: n['order_id']),
                                ),
                              );
                            }
                          },
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: isRead ? const Color(0xFFF1F5F9) : const Color(0xFF2563EB).withOpacity(0.1),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              n['icon'] == 'clock'
                                  ? Icons.watch_later_outlined
                                  : n['icon'] == 'refresh-cw'
                                      ? Icons.refresh_rounded
                                      : Icons.notifications_active_outlined,
                              color: isRead ? const Color(0xFF64748B) : const Color(0xFF2563EB),
                              size: 20,
                            ),
                          ),
                          title: Text(
                            n['title'] ?? 'Info L-DRY',
                            style: TextStyle(
                              fontWeight: isRead ? FontWeight.normal : FontWeight.bold,
                              fontSize: 14,
                              color: const Color(0xFF0F172A),
                            ),
                          ),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 4),
                              Text(
                                n['message'] ?? '',
                                style: const TextStyle(fontSize: 12, color: Color(0xFF64748B), height: 1.3),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                n['created_at'] ?? '',
                                style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8)),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}

// ======================== REWARDS & VOUCHERS SCREEN ========================
class RewardsScreen extends StatefulWidget {
  const RewardsScreen({super.key});

  @override
  State<RewardsScreen> createState() => _RewardsScreenState();
}

class _RewardsScreenState extends State<RewardsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  List<dynamic> _rewards = [];
  List<dynamic> _redemptions = [];
  List<dynamic> _transactions = [];
  int _userPoints = parseInt(AppConfig.userProfile?['points']);
  bool _isLoadingRewards = true;
  bool _isLoadingVouchers = true;
  bool _isLoadingHistory = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _fetchRewards();
    _fetchVouchers();
    _fetchTransactions();
    _refreshProfilePoints();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _refreshProfilePoints() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/user'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          setState(() {
            AppConfig.userProfile = data['user'];
            _userPoints = parseInt(AppConfig.userProfile?['points']);
          });
        }
      }
    } catch (e) {
      // silent
    }
  }

  Future<void> _fetchRewards() async {
    try {
      print('DEBUG: Fetching rewards from ${AppConfig.baseUrl}/api/rewards');
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/rewards'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      print('DEBUG: Rewards status code: ${response.statusCode}');
      print('DEBUG: Rewards response body: ${response.body}');
      final data = jsonDecode(response.body);
      setState(() {
        _rewards = data['rewards'] ?? [];
        _isLoadingRewards = false;
      });
    } catch (e) {
      print('DEBUG: Exception in _fetchRewards: $e');
      setState(() => _isLoadingRewards = false);
    }
  }

  Future<void> _fetchVouchers() async {
    try {
      print('DEBUG: Fetching vouchers from ${AppConfig.baseUrl}/api/points/redemptions');
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/points/redemptions'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      print('DEBUG: Vouchers status code: ${response.statusCode}');
      print('DEBUG: Vouchers response body: ${response.body}');
      final data = jsonDecode(response.body);
      setState(() {
        _redemptions = data['redemptions'] ?? [];
        _isLoadingVouchers = false;
      });
    } catch (e) {
      print('DEBUG: Exception in _fetchVouchers: $e');
      setState(() => _isLoadingVouchers = false);
    }
  }

  Future<void> _fetchTransactions() async {
    try {
      print('DEBUG: Fetching transactions from ${AppConfig.baseUrl}/api/points/transactions');
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/api/points/transactions'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      print('DEBUG: Transactions status code: ${response.statusCode}');
      print('DEBUG: Transactions response body: ${response.body}');
      final data = jsonDecode(response.body);
      setState(() {
        _transactions = data['transactions'] ?? [];
        _isLoadingHistory = false;
      });
    } catch (e) {
      print('DEBUG: Exception in _fetchTransactions: $e');
      setState(() => _isLoadingHistory = false);
    }
  }

  Future<void> _redeemReward(int id, String name, int cost) async {
    // Show confirmation dialog first
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Tukarkan Poin', style: TextStyle(fontWeight: FontWeight.bold)),
        content: Text('Apakah Anda yakin ingin menukarkan $cost poin untuk mendapatkan "$name"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal', style: TextStyle(color: Color(0xFF64748B))),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF2563EB), foregroundColor: Colors.white),
            child: const Text('Tukarkan'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() {
      _isLoadingRewards = true;
      _isLoadingVouchers = true;
      _isLoadingHistory = true;
    });

    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/rewards/$id/redeem'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${AppConfig.token}',
        },
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        // Show success alert with code
        if (!mounted) return;
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text('🎉 Klaim Berhasil!', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.green)),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Anda berhasil menukarkan "$name".'),
                const SizedBox(height: 16),
                const Text('Kode Voucher Anda:', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF64748B))),
                const SizedBox(height: 6),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        data['redemption']['code'],
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, fontFamily: 'monospace'),
                      ),
                      IconButton(
                        icon: const Icon(Icons.copy, size: 18),
                        onPressed: () {
                          Clipboard.setData(ClipboardData(text: data['redemption']['code']));
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Kode voucher disalin!'), duration: Duration(seconds: 1)),
                          );
                        },
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                const Text(
                  'Tunjukkan kode ini ke admin atau kurir untuk diskon.',
                  style: TextStyle(fontSize: 11, color: Color(0xFF64748B), fontStyle: FontStyle.italic),
                ),
              ],
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Selesai', style: TextStyle(color: Color(0xFF2563EB), fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        );

        _fetchRewards();
        _fetchVouchers();
        _fetchTransactions();
        _refreshProfilePoints();
      } else {
        throw Exception(data['message'] ?? 'Gagal menukarkan reward');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.redAccent),
        );
      }
      _fetchRewards();
      _fetchVouchers();
      _fetchTransactions();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Hadiah & Poin', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF0F172A),
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: const Color(0xFF2563EB),
          unselectedLabelColor: const Color(0xFF64748B),
          indicatorColor: const Color(0xFF2563EB),
          labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
          tabs: const [
            Tab(text: 'Katalog Hadiah'),
            Tab(text: 'Voucher Saya'),
            Tab(text: 'Riwayat Poin'),
          ],
        ),
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Poin loyalty info banner
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              color: const Color(0xFF2563EB),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Total Poin Aktif Anda',
                        style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w500),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        '$_userPoints Pts',
                        style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  const Icon(Icons.stars_rounded, color: Colors.amber, size: 36),
                ],
              ),
            ),
            Expanded(
              child: TabBarView(
                controller: _tabController,
                children: [
                  // Tab 1: Katalog Hadiah
                  _buildCatalogTab(),
                  // Tab 2: Voucher Saya
                  _buildVouchersTab(),
                  // Tab 3: Riwayat Poin
                  _buildHistoryTab(),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCatalogTab() {
    if (_isLoadingRewards) {
      return const Center(child: CircularProgressIndicator(color: Color(0xFF2563EB)));
    }

    if (_rewards.isEmpty) {
      return const Center(child: Text('Tidak ada hadiah tersedia saat ini', style: TextStyle(color: Color(0xFF64748B))));
    }

    return RefreshIndicator(
      onRefresh: () async {
        await _fetchRewards();
        await _refreshProfilePoints();
      },
      color: const Color(0xFF2563EB),
      child: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: _rewards.length,
        itemBuilder: (context, index) {
          final r = _rewards[index];
          final pointsCost = parseInt(r['points_cost']);
          final bool canAfford = _userPoints >= pointsCost;
          final bool isVoucher = r['name'].toString().toLowerCase().contains('voucher') || r['name'].toString().toLowerCase().contains('diskon');

          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: const Color(0xFFF1F5F9)),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.01),
                  blurRadius: 8,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF59E0B).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(
                    isVoucher ? Icons.confirmation_number_outlined : Icons.card_giftcard_outlined,
                    color: const Color(0xFFF59E0B),
                    size: 28,
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        r['name'] ?? '',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        r['description'] ?? '',
                        style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), height: 1.3),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        '$pointsCost Poin',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF2563EB)),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                ElevatedButton(
                  onPressed: canAfford ? () => _redeemReward(r['id'], r['name'], pointsCost) : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563EB),
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: const Color(0xFFE2E8F0),
                    disabledForegroundColor: const Color(0xFF94A3B8),
                    elevation: 0,
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: const Text('Tukarkan', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildVouchersTab() {
    if (_isLoadingVouchers) {
      return const Center(child: CircularProgressIndicator(color: Color(0xFF2563EB)));
    }

    if (_redemptions.isEmpty) {
      return const Center(child: Text('Anda belum menukarkan voucher apapun', style: TextStyle(color: Color(0xFF64748B))));
    }

    return RefreshIndicator(
      onRefresh: _fetchVouchers,
      color: const Color(0xFF2563EB),
      child: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: _redemptions.length,
        itemBuilder: (context, index) {
          final red = _redemptions[index];
          final status = red['status'] ?? 'active';
          final bool isActive = status == 'active';

          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: const Color(0xFFF1F5F9)),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.01),
                  blurRadius: 8,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        red['reward_name'] ?? 'Voucher',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: isActive ? Colors.green.withOpacity(0.1) : const Color(0xFFE2E8F0),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        isActive ? 'AKTIF' : status.toString().toUpperCase(),
                        style: TextStyle(
                          color: isActive ? Colors.green : const Color(0xFF64748B),
                          fontWeight: FontWeight.bold,
                          fontSize: 10,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  'Klaim: ${red['created_at']} (${red['points_spent']} Pts)',
                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                ),
                const Divider(height: 24, color: Color(0xFFF1F5F9)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('KODE VOUCHER', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8))),
                          const SizedBox(height: 2),
                          Text(
                            red['code'] ?? '',
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, fontFamily: 'monospace'),
                          ),
                        ],
                      ),
                      if (isActive)
                        IconButton(
                          icon: const Icon(Icons.copy, size: 18, color: Color(0xFF2563EB)),
                          onPressed: () {
                            Clipboard.setData(ClipboardData(text: red['code'] ?? ''));
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('Kode voucher disalin!'), duration: Duration(seconds: 1)),
                            );
                          },
                        ),
                    ],
                  ),
                ),
                if (isActive) ...[
                  const SizedBox(height: 10),
                  const Text(
                    'Tunjukkan kode ini ke admin atau kurir saat pembayaran.',
                    style: TextStyle(fontSize: 10, color: Color(0xFF64748B), fontStyle: FontStyle.italic),
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildHistoryTab() {
    if (_isLoadingHistory) {
      return const Center(child: CircularProgressIndicator(color: Color(0xFF2563EB)));
    }

    if (_transactions.isEmpty) {
      return const Center(child: Text('Belum ada riwayat transaksi poin', style: TextStyle(color: Color(0xFF64748B))));
    }

    return RefreshIndicator(
      onRefresh: _fetchTransactions,
      color: const Color(0xFF2563EB),
      child: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: _transactions.length,
        itemBuilder: (context, index) {
          final t = _transactions[index];
          final amount = parseInt(t['amount']);
          final isPositive = amount > 0;

          return Container(
            padding: const EdgeInsets.symmetric(vertical: 14),
            decoration: const BoxDecoration(
              border: Border(bottom: BorderSide(color: Color(0xFFF1F5F9))),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        t['description'] ?? '',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(height: 4),
                      Text(t['created_at'] ?? '', style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8))),
                    ],
                  ),
                ),
                Text(
                  '${isPositive ? '+' : ''}$amount Pts',
                  style: TextStyle(
                    fontWeight: FontWeight.w900,
                    fontSize: 14,
                    color: isPositive ? Colors.green : Colors.redAccent,
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
