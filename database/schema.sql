CREATE DATABASE IF NOT EXISTS globetrek_adventures
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE globetrek_adventures;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS queries;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS packages;
DROP TABLE IF EXISTS accommodations;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS guides;
DROP TABLE IF EXISTS destinations;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(40) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'staff', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    country VARCHAR(120) NOT NULL,
    summary TEXT NOT NULL,
    best_season VARCHAR(80) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE accommodations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    name VARCHAR(140) NOT NULL,
    type VARCHAR(80) NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    rating DECIMAL(3,2) NOT NULL DEFAULT 4.50,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    type VARCHAR(80) NOT NULL,
    capacity INT NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
);

CREATE TABLE guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    languages VARCHAR(180) NOT NULL,
    specialty VARCHAR(140) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    rating DECIMAL(3,2) NOT NULL DEFAULT 4.70,
    image VARCHAR(255) NOT NULL,
    bio TEXT NOT NULL
);

CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    activities TEXT NOT NULL,
    duration_days INT NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    activity_level VARCHAR(80) NOT NULL,
    image VARCHAR(255) NOT NULL,
    status ENUM('active', 'paused') NOT NULL DEFAULT 'active',
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    destination_id INT NOT NULL,
    accommodation_id INT DEFAULT NULL,
    vehicle_id INT DEFAULT NULL,
    guide_id INT DEFAULT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    travelers INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending_payment', 'paid', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_payment',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE SET NULL
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(80) NOT NULL,
    status ENUM('paid', 'failed') NOT NULL DEFAULT 'paid',
    transaction_ref VARCHAR(80) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open', 'answered', 'closed') NOT NULL DEFAULT 'open',
    response TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (name, email, phone, password_hash, role) VALUES
('System Administrator', 'admin@globetrek.local', '+94 77 000 0001', '$2y$10$mlOs2uc1j5/eAmrS2baYbebrniPoZyvfN9x7MPGLGK.qOxrltZFjK', 'admin'),
('Travel Staff', 'staff@globetrek.local', '+94 77 000 0002', '$2y$10$b/JBUGs6PFBe0nzWXhjnj.3wj9EmJLBzxqyVttETkWPp.LzK1mhAO', 'staff'),
('Demo Customer', 'customer@globetrek.local', '+94 77 000 0003', '$2y$10$aLPau6sj19RrLRzFSIWK6O/ktPVVu2RcBsdf1Noci6mqEMVUPbFoy', 'customer');

INSERT INTO destinations (name, country, summary, best_season, image) VALUES
('Bali', 'Indonesia', 'Tropical villas, beach clubs, rice terraces, temples, and relaxed island escapes.', 'May to September', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=900&q=80'),
('Paris', 'France', 'Romantic boulevards, museums, boutique stays, river cruises, and cafe culture.', 'April to June', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=900&q=80'),
('Dubai', 'United Arab Emirates', 'Luxury hotels, desert safaris, architecture tours, shopping, and family entertainment.', 'November to March', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=900&q=80'),
('Maldives', 'Maldives', 'Overwater villas, clear lagoons, private island resorts, and ocean adventures.', 'December to April', 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=900&q=80'),
('Kyoto', 'Japan', 'Temple walks, traditional inns, gardens, tea culture, and quiet heritage streets.', 'March to May', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=900&q=80');

INSERT INTO accommodations (destination_id, name, type, price_per_night, rating, image, description) VALUES
(1, 'Ubud Canopy Villa', 'Villa', 145.00, 4.86, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80', 'A private villa with pool, breakfast, and rice terrace access.'),
(2, 'Seine Boutique Hotel', 'Boutique Hotel', 185.00, 4.72, 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=900&q=80', 'A refined central stay close to museums, cafes, and river walks.'),
(3, 'Marina City Suites', 'City Hotel', 155.00, 4.70, 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=900&q=80', 'Premium city hotel with skyline views and metro access.'),
(4, 'Azure Reef Water Villa', 'Water Villa', 320.00, 4.94, 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=900&q=80', 'Overwater stay with direct lagoon access and sunset dining.'),
(5, 'Kyoto Garden Ryokan', 'Ryokan', 210.00, 4.88, 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80', 'A calm traditional stay with garden views and tea service.');

INSERT INTO vehicles (name, type, capacity, price_per_day, image, description) VALUES
('Comfort Sedan', 'Car', 3, 42.00, 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=900&q=80', 'Comfortable air-conditioned car for couples or small families.'),
('Family Van', 'Van', 7, 75.00, 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=900&q=80', 'Spacious van for group tours with luggage capacity.'),
('Adventure 4x4', 'SUV', 4, 95.00, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=900&q=80', 'SUV suitable for hill country and safari routes.'),
('Airport Shuttle', 'Mini Bus', 14, 125.00, 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=900&q=80', 'Shared shuttle for airport pickups and group transfers.');

INSERT INTO guides (name, languages, specialty, price_per_day, rating, image, bio) VALUES
('Maya Santoso', 'English, Indonesian', 'Island wellness and culture', 58.00, 4.88, 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=700&q=80', 'Bali host for temples, cafes, rice terraces, and relaxed island itineraries.'),
('Claire Dubois', 'English, French', 'Art, food, and city walks', 72.00, 4.91, 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=700&q=80', 'Paris guide for museums, hidden streets, restaurants, and river evenings.'),
('Leah Morgan', 'English, Arabic', 'Dubai city experiences', 65.00, 4.79, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=700&q=80', 'Dubai destination host for family itineraries, shopping, and desert trips.'),
('Aiko Tanaka', 'English, Japanese', 'Heritage gardens and tea culture', 68.00, 4.93, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=700&q=80', 'Kyoto specialist for quiet temples, old streets, gardens, and food experiences.');

INSERT INTO packages (destination_id, title, slug, description, activities, duration_days, base_price, activity_level, image, status) VALUES
(1, 'Bali Island Reset', 'bali-island-reset', 'A slow tropical escape with private villas, rice terraces, beach clubs, temples, and wellness time.', 'Ubud terraces, temple visit, beach club, spa evening', 5, 740.00, 'Relaxed', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1000&q=80', 'active'),
(2, 'Paris City Romance', 'paris-city-romance', 'A minimal city break with boutique stays, museum time, river views, and curated dining.', 'Louvre walk, Seine cruise, Montmartre, cafe dinner', 4, 920.00, 'Easy', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=1000&q=80', 'active'),
(3, 'Dubai Family City Break', 'dubai-family-city-break', 'A compact international package with city landmarks, marina evenings, desert safari, and shopping time.', 'Burj Khalifa, Dubai Mall, desert safari, marina walk', 5, 880.00, 'Easy', 'https://images.unsplash.com/photo-1526495124232-a04e1849168c?auto=format&fit=crop&w=1000&q=80', 'active'),
(4, 'Maldives Lagoon Escape', 'maldives-lagoon-escape', 'A premium island stay with overwater villas, snorkeling, sunset cruises, and quiet beach days.', 'Snorkeling, sunset cruise, private beach dinner, spa', 5, 1280.00, 'Relaxed', 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1000&q=80', 'active'),
(5, 'Kyoto Heritage Calm', 'kyoto-heritage-calm', 'A refined cultural itinerary with gardens, temples, tea ceremonies, and traditional stays.', 'Temple walk, tea ceremony, old town evening, garden visit', 4, 980.00, 'Easy', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1000&q=80', 'active');

INSERT INTO queries (user_id, name, email, subject, message, status) VALUES
(3, 'Demo Customer', 'customer@globetrek.local', 'Need airport pickup', 'Can I add airport pickup to a Dubai package?', 'open');
