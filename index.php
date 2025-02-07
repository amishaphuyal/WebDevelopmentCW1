<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontline Hospital - Hospital Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #3b82f6;
            --accent: #34d399;
            --dark: #1e293b;
            --light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            color: var(--accent);
        }

       
        /* Hero Section */
        .hero {
            padding: 120px 2rem 2rem;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            color: white;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            max-width: 600px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s 0.3s forwards;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s 0.6s forwards;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            box-shadow: 0 10px 20px rgba(52, 211, 153, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        /* Features Section */
        .features {
            padding: 5rem 2rem;
            background: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
        }

        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* About Section */
        .about {
            padding: 5rem 2rem;
            background: #f8fafc;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .about-text p {
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .about-image {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Stats Section */
        .stats {
            padding: 4rem 2rem;
            background: var(--primary);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        /* Team Section */
.team {
    padding: 5rem 2rem;
    background: #f8fafc;
    text-align: center;
}

.team-container {
    max-width: 1200px;
    margin: auto;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #64748b;
    margin-bottom: 2.5rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.team-member {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.team-member:hover {
    transform: translateY(-5px);
}

.team-member img {
    width: 100%;
    border-radius: 10px;
}

.team-member h4 {
    font-size: 1.5rem;
    margin-top: 1rem;
    color: var(--primary);
}

.team-member p {
    font-size: 1rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 4rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .footer-section h4 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #94a3b8;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        /* Animations */
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            nav ul {
                display: none;
            }
        }

nav ul {
    display: flex;
    gap: 2rem;
    list-style: none;
    align-items: center;
}

nav a {
    color: var(--dark);
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    padding: 0.5rem 1rem;
    display: block;
}

nav a:not(.nav-cta)::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: width 0.3s ease;
}

nav a:not(.nav-cta):hover::after {
    width: 80%;
}

.nav-cta {
    background: var(--accent);
    color: white !important;
    border-radius: 50px;
    padding: 0.8rem 2rem !important;
    transform: scale(1);
    box-shadow: 0 4px 12px rgba(52, 211, 153, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-cta:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(52, 211, 153, 0.4);
}

/* Mobile-responsive menu toggle */
.menu-toggle {
    display: none;
    cursor: pointer;
    font-size: 1.5rem;
    color: var(--dark);
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    
    nav ul {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        flex-direction: column;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    nav ul.active {
        display: flex;
    }
    
    nav a {
        padding: 1rem !important;
        text-align: center;
    }
    
    .nav-cta {
        margin-top: 1rem;
    }
}
        /* Scroll Animation */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-hospital"></i>
            Frontline
        </div>
    <nav>
      <ul>
        <li><a href="#features">Features</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#team">Team</a></li>
        <li><a href="login.php" class="nav-cta">Login</a></li>
       </ul>
    </nav> 
    </header>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Transform Your Healthcare Management</h1>
            <p>Streamline hospital operations, enhance patient care, and optimize workflows with our intelligent management solution.</p>
            <div class="cta-buttons">
                <a href="#features" class="btn btn-primary">Explore Features</a>
                <a href="#contact" class="btn btn-secondary">Schedule Demo</a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="features-grid">
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Appointment Management</h3>
                <p>Efficiently schedule and track patient appointments with real-time updates.</p>
            </div>
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3>Doctor Dashboard</h3>
                <p>Comprehensive interface for medical professionals to manage patient care.</p>
            </div>
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h3>Digital Records</h3>
                <p>Secure cloud-based storage for all medical records and patient history.</p>
            </div>
        </div>
    </section>

    <section class="about" id="about">
        <div class="about-content">
            <div class="about-image scroll-reveal">
                <img src="images/frontline_hospital.jpg" alt="Hospital Management">
            </div>
            <div class="about-text">
                <h2 class="scroll-reveal">Revolutionizing Healthcare Management</h2>
                <p class="scroll-reveal">Our platform integrates cutting-edge technology with user-friendly design to create the ultimate hospital management solution.</p>
                <p class="scroll-reveal">With over 10 years of experience in healthcare technology, we understand the unique challenges faced by medical institutions.</p>
                <a href="#contact" class="btn btn-primary scroll-reveal">Learn More</a>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item scroll-reveal">
                <div class="stat-number">150+</div>
                <div>Healthcare Partners</div>
            </div>
            <div class="stat-item scroll-reveal">
                <div class="stat-number">1M+</div>
                <div>Patients Served</div>
            </div>
            <div class="stat-item scroll-reveal">
                <div class="stat-number">98%</div>
                <div>User Satisfaction</div>
            </div>
        </div>
    </section>

    <section class="team" id="team">
    <div class="team-container">
        <h2 class="section-title">Meet Our Doctors</h2>
        <p class="section-subtitle">Highly experienced professionals dedicated to your healthcare.</p>

        <div class="team-grid">
            <div class="team-member">
                <img src="images/Aayesha timalsina.jpg" alt="Dr. Aayesha Timalsina">
                <h4>Dr. Aayesha Timalsina</h4>
                <p>General Practitioner</p>
            </div>
            <div class="team-member">
                <img src="images/Dr Bikram jha.jpg" alt="Dr. Bikram Jhan">
                <h4>Dr. Bikram Jhan</h4>
                <p>Ophthalmologist</p>
            </div>
            <div class="team-member">
                <img src="images/saheli dangol.jpg" alt="Dr. Saheli dangol">
                <h4>Dr. Saheli Dangol</h4>
                <p>Paediatrician</p>
            </div>
            <div class="team-member">
                <img src="images/ranjit-kumar-sharma.jpeg" alt="Dr. Ranjit Kumar Sharma">
                <h4>Dr. Ranjit Kumar Sharma</h4>
                <p>Cardiologist</p>
            </div>
        </div>
    </div>
</section>


    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Frontline Hospital</h4>
                <p>Transforming healthcare through innovative technology solutions.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 10 Dillibazar, Kathmandu</li>
                    <li><i class="fas fa-phone"></i> (+977) 9845012693</li>
                    <li><i class="fas fa-envelope"></i> info@frontline.com</li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>



    <script>
        // Scroll Reveal Animation
        const scrollReveal = document.querySelectorAll('.scroll-reveal');

        const revealOnScroll = () => {
            scrollReveal.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;

                if (elementTop < windowHeight - 100) {
                    element.classList.add('visible');
                }
            });
        };

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>