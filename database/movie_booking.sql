-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 12:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movie booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$g8tAo9LKArYvM9zGxD2GSOBo0dBgk57DMjtiPNjjWgvKJL2V6ALo2');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `seat_class` enum('Gold','Platinum','Box') NOT NULL,
  `adult_seats` int(11) DEFAULT 0,
  `kid_seats` int(11) DEFAULT 0,
  `total_amount` decimal(8,2) NOT NULL,
  `discount_amount` decimal(8,2) DEFAULT 0.00,
  `coupon_code` varchar(30) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Confirmed','Cancelled') DEFAULT 'Confirmed',
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT 'Cash at Counter',
  `payment_status` enum('Pending','Paid','Failed') NOT NULL DEFAULT 'Paid',
  `stripe_session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `show_id`, `seat_class`, `adult_seats`, `kid_seats`, `total_amount`, `discount_amount`, `coupon_code`, `booking_date`, `status`, `contact_email`, `payment_method`, `payment_status`, `stripe_session_id`) VALUES
(1, 1, 24, 'Gold', 1, 5, 44467.50, 0.00, NULL, '2026-08-14 09:34:38', 'Confirmed', NULL, 'Cash at Counter', 'Paid', NULL),
(2, 1, 6, 'Gold', 1, 0, 11485.00, 0.00, NULL, '2026-08-14 09:55:48', 'Confirmed', 'mustafafaisal542@gmail.com', 'EasyPaisa', 'Paid', NULL),
(3, 1, 5, 'Gold', 1, 6, 40576.00, 0.00, NULL, '2026-08-14 09:57:15', 'Confirmed', 'mustafarangeela786@gmail.com', 'Cash at Counter', 'Paid', NULL),
(4, 1, 13, 'Gold', 1, 5, 42819.00, 0.00, NULL, '2026-08-14 09:57:50', 'Confirmed', 'mustafafaisal542@gmail.com', 'Credit/Debit Card', 'Paid', NULL),
(5, 1, 6, 'Gold', 1, 6, 45940.00, 0.00, NULL, '2026-08-14 09:59:32', 'Confirmed', 'hunaidaptech@gmail.com', 'JazzCash', 'Paid', NULL),
(6, 1, 36, 'Gold', 19, 8, 283107.00, 0.00, NULL, '2026-08-14 10:00:02', 'Confirmed', 'hunaidaptech@gmail.com', 'EasyPaisa', 'Paid', NULL),
(7, 1, 36, 'Gold', 19, 8, 283107.00, 0.00, NULL, '2026-08-14 10:00:05', 'Confirmed', 'hunaidaptech@gmail.com', 'EasyPaisa', 'Paid', NULL),
(8, 1, 36, 'Gold', 19, 8, 283107.00, 0.00, NULL, '2026-08-14 10:00:09', 'Confirmed', 'hunaidaptech@gmail.com', 'EasyPaisa', 'Paid', NULL),
(10, 1, 3, 'Gold', 16, 5, 186868.50, 0.00, NULL, '2026-08-15 08:42:55', 'Confirmed', 'mustafarangeela786@gmail.com', 'Credit/Debit Card', 'Paid', NULL),
(11, 1, 6, 'Gold', 24, 6, 310095.00, 0.00, NULL, '2026-08-15 15:22:54', 'Confirmed', 'mustafarangeela786@gmail.com', 'PayPal', 'Paid', NULL),
(12, 1, 7, 'Gold', 14, 5, 197026.50, 0.00, NULL, '2026-08-15 15:56:15', 'Confirmed', 'mahadaptech123@gmail.com', 'PayPal', 'Paid', NULL),
(13, 1, 58, 'Gold', 1, 0, 12236.00, 0.00, NULL, '2026-08-15 19:41:31', 'Confirmed', 'mustafarangeela786@gmail.com', 'Credit/Debit Card', 'Paid', NULL),
(14, 1, 58, 'Gold', 14, 3, 189658.00, 0.00, NULL, '2026-08-15 20:08:56', 'Confirmed', 'mustafarangeela786@gmail.com', 'Credit/Debit Card', 'Paid', NULL),
(16, 1, 538, 'Platinum', 13, 6, 34592.00, 0.00, NULL, '2026-08-16 12:27:49', 'Confirmed', 'Mustafafaisalmustafafaisal5@gmail.com', 'PayPal', 'Paid', NULL),
(17, 1, 365, 'Gold', 10, 2, 9266.40, 1029.60, 'WELCOME10', '2026-08-16 12:30:02', 'Confirmed', 'mustafarangeela786@gmail.com', 'Credit/Debit Card', 'Paid', NULL),
(18, 1, 58, 'Gold', 8, 5, 128478.00, 0.00, NULL, '2026-08-16 17:31:38', 'Confirmed', 'mustafarangeela786@gmail.com', 'JazzCash', 'Paid', NULL),
(19, 1, 3, 'Gold', 10, 4, 109090.80, 12121.20, 'WELCOME10', '2026-08-17 12:32:31', 'Confirmed', 'mustafarangeela786@gmail.com', 'PayPal', 'Paid', NULL),
(20, 1, 539, 'Gold', 14, 6, 12561.30, 1395.70, 'WELCOME10', '2026-08-17 14:10:25', 'Confirmed', 'mustafarangeela786@gmail.com', 'JazzCash', 'Paid', NULL),
(21, 1, 668, 'Platinum', 23, 20, 363000.00, 363000.00, 'LEASBIAN', '2026-08-18 08:08:12', 'Confirmed', 'mustafarangeela786@gmail.com', 'EasyPaisa', 'Paid', NULL),
(22, 1, 384, 'Gold', 15, 2, 12398.40, 1377.60, 'WELCOME10', '2026-08-18 08:18:02', 'Confirmed', 'mustafarangeela786@gmail.com', 'JazzCash', 'Paid', NULL),
(23, 1, 287, 'Gold', 1, 0, 971.00, 0.00, NULL, '2026-08-18 10:10:30', 'Confirmed', 'mustafarangeela786@gmail.com', 'Cash at Counter', 'Paid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `seat_code` varchar(10) NOT NULL,
  `seat_class` varchar(20) NOT NULL,
  `ticket_type` enum('Adult','Kid') NOT NULL DEFAULT 'Adult',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `discount_percent` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `expires_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `code`, `discount_percent`, `active`, `expires_on`) VALUES
(1, 'WELCOME10', 10, 1, '2026-11-14'),
(2, 'MOVIE20', 20, 1, '2026-11-14'),
(3, 'XXX', 20, 1, '2026-09-12'),
(4, 'LEASBIAN', 50, 1, '2027-02-03');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `industry` varchar(20) DEFAULT NULL,
  `quality_tag` varchar(20) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `genre`, `language`, `industry`, `quality_tag`, `duration_minutes`, `description`, `trailer_url`, `poster`, `release_date`, `is_featured`, `created_at`) VALUES
(1, 'Bloody Shot', 'Action', 'English', 'Hollywood', 'HD', 345, 'Bloodshot is a 2020 American superhero film starring Vin Diesel. Based on the Valiant Comics character Bloodshot, it follows a soldier who was killed in action, only to be brought back to life with superpowers by an organization that wants to use him as a weapon. The film was directed by David S. F. Wilson and written by Jeff Wadlow and Eric Heisserer. Eiza González, Sam Heughan, Toby Kebbell, Lamorne Morris, and Guy Pearce appear in supporting roles.', 'https://youtu.be/vOUVVDWdXbo?si=WmEcEHGW5FCOR0r1', 'https://tse2.mm.bing.net/th/id/OIP.3bxF0OqNwyy9DWL0qBfX5AHaJQ?r=0&amp;amp;rs=1&amp;amp;pid=ImgDetMain&amp;amp;o=7&amp;amp;rm=3', '2026-09-05', 1, '2026-08-14 04:17:22'),
(2, 'IT (2017)', 'horror', 'English', 'Hollywood', 'HD', 338, 'IT — Fear Has a New Face A group of children must confront their deepest fears when a terrifying supernatural creature begins haunting their town. Together, they discover that facing their fears may be their only chance to survive.', 'https://www.youtube.com/embed/FnCdOQsX5kc', 'https://tse3.mm.bing.net/th/id/OIP.D2eL_gQDT9CYHJFhJ9whKgHaK3?r=0&amp;amp;amp;rs=1&amp;amp;amp;pid=ImgDetMain&amp;amp;amp;o=7&amp;amp;amp;rm=3', '2026-09-10', 0, '2026-08-14 04:17:22'),
(3, 'The Batman: Part II', 'Action', 'English', 'Hollywood', 'HD', 317, 'The Batman: Part II is set to delve deeper into the mystery of Batman&#039;s alter ego, Bruce Wayne, and his investigation of further corruption in Gotham City. The film will feature Robert Pattinson reprising his role as Batman, with Matt Reeves returning as director and Mattson Tomlin as his writing partner. The sequel is expected to explore Batman&#039;s detective aspects more than in previous films, drawing inspiration from the works of Alfred Hitchcock and the New Hollywood era. The plot will be under wraps, but it is anticipated to build upon the events of the spin-off television series The Penguin (2024). The film&#039;s production has faced delays due to the Hollywood strikes and personal issues for Reeves, but it remains a highly anticipated addition to the Batman franchise.', 'https://youtu.be/aG8U7-XK7PI?si=10RT7c1PzfJpUFqk', 'https://tse4.mm.bing.net/th/id/OIP.64_WTsyOyykcyfr5Roe9SgHaJ4?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-26', 0, '2026-08-14 04:17:22'),
(4, 'One Battle After Another', 'Action', 'English', 'Hollywood', 'HD', 308, 'One Battle After Another is a 2025 dark comedy-thriller about a washed-up revolutionary and his daughter confronting the consequences of past activism while evading a dangerous nemesis.\r\nThe film follows Bob (Leonardo DiCaprio), a former radical living off-grid in paranoia, alongside his spirited, self-reliant teenage daughter, Willa (Chase Infiniti). Sixteen years after his revolutionary past, Bob’s old enemy, Col. Steven J. Lockjaw (Sean Penn), resurfaces, and Willa disappears, forcing Bob to confront his past and race against time to rescue her', 'https://youtu.be/feOQFKv2Lw4?si=80wlqAaT_8qCzE5m', 'https://tse3.mm.bing.net/th/id/OIP.NQ9mD-ceZ9GTTmPlzKl_aQHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-21', 0, '2026-08-14 04:17:22'),
(5, 'Avengers 5', 'Action', 'English', 'Hollywood', 'HD', 373, 'Avengers: Doomsday is an upcoming American superhero film based on the Marvel Comics superhero team the Avengers. Produced by Marvel Studios and distributed by Walt Disney Studios Motion Pictures, it is intended to be the sequel to Avengers: Endgame (2019) and the 39th film in the Marvel Cinematic Universe (MCU). Directed by Anthony and Joe Russo, and written by Michael Waldron, Stephen McFeely, and the writing team of Chris McKenna and Erik Sommers, the film features an ensemble cast led by Robert Downey Jr. as Doctor Doom. In the film, the Avengers, Wakandans, New Avengers, Fantastic Four, and the X-Men converge from different universes to face Doom.', 'https://youtu.be/KYsoip-6XEQ?si=ec4UNoDcXeSVK7B8', 'https://tse1.mm.bing.net/th/id/OIP.kvIDIvWFS76nnef2KsiojgHaL1?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-14', 0, '2026-08-14 04:17:22'),
(6, 'Godzila Minus Zero', 'Action', 'English', 'Hollywood', 'HD', 256, 'Godzilla Minus Zero (Japanese: ゴジラ-0.0マイナスゼロ, Hepburn: Gojira Mainasu Zero) is an upcoming Japanese kaiju film written, directed, and with visual effects by Takashi Yamazaki. A sequel to Godzilla Minus One (2023), it is the 39th film in the Godzilla franchise, the 34th film thereof produced by Toho, and the sixth installment in the franchise&amp;#039;s Reiwa era.[a] The film stars Ryunosuke Kamiki, Minami Hamabe, Hidetaka Yoshioka, Yuki Yamada, Kuranosuke Sasaki, and Sakura Ando reprising their roles from Minus One, with Min Tanaka playing a new character.', 'https://youtu.be/tCyyloFIQ_I?si=cAHBEluXFZbsRLGF', 'https://tse1.mm.bing.net/th/id/OIP.LYr7eOQe63BzP1cEtCRXtgAAAA?r=0&amp;amp;rs=1&amp;amp;pid=ImgDetMain&amp;amp;o=7&amp;amp;rm=3', '2026-11-06', 0, '2026-08-14 04:17:22'),
(7, 'Clay Face', 'Action', 'English', 'Hollywood', 'HD', 219, 'The film follows Matt Hagen, an up-and-coming Hollywood actor whose face is disfigured by a Gotham City gangster. Desperate for a solution, he turns to Dr. Caitlin Bates, a fringe scientist, who transforms his body into clay. This transformation grants him shape-shifting abilities, but also strips him of his identity and humanity, leading him down a path of revenge and monstrous behavior. The story explores themes of loss of identity, corrosive love, and the dark side of scientific ambition while charting Hagen’s descent from stardom to villainy', 'https://youtu.be/pJt96whxunA?si=rkxxFLt6k0f7dw4i', 'https://whentostream.com/wp-content/uploads/2026/04/Clayface-Horizontal.jpeg', '2026-09-19', 0, '2026-08-14 04:17:22'),
(8, 'Coyote vs Acme', 'Action', 'English', 'Hollywood', 'HD', 216, 'The film follows Wile E. Coyote, who, after decades of catastrophic product failures from the Acme Corporation, enlists a down-on-his-luck human billboard attorney, Kevin Avery (Will Forte), to sue the company for selling faulty products that repeatedly backfire on him in his pursuit of the Road Runner \r\nWikipedia\r\nWikipedia\r\n+2\r\n. The story escalates as Kevin discovers that his former law firm’s intimidating boss, Buddy Crane (John Cena), now represents Acme, leading to a high-stakes legal battle. The movie blends slapstick chaos with courtroom comedy, featuring classic Looney Tunes-style gags like dynamite, boulders, magnets, and rocket skates', 'https://youtu.be/5rVMJGlXCoE?si=3VELtLe0Z4wPWrRM', 'https://tse2.mm.bing.net/th/id/OIP.La122AVQhxXo8SztSa6RjgHaJQ?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-28', 0, '2026-08-14 04:17:22'),
(9, 'REACHER SEASON 4', 'Action', 'Engl', 'Hollywood', 'IMAX', 258, 'Reacher is an American action crime television series developed by Nick Santora for Amazon Prime Video. Based on the Jack Reacher novel series by Lee Child, it stars Alan Ritchson as the title character, a self-proclaimed drifter and former U.S. Army military police officer with formidable strength, intellect, and abilities. During his travels, Reacher crosses paths with dangerous criminals and battles them.', 'https://youtu.be/Leg1aL7xx-g?si=ccBTyi1qCra5bXpr', 'https://tse4.mm.bing.net/th/id/OIP.PUOthkOQy8mRLuAJYENQxQHaJM?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-14', 0, '2026-08-14 04:17:22'),
(10, 'The Notebook', 'Romance', 'English', 'Hollywood', 'HD', 150, 'A passionate love story follows two young people whose bond endures despite separation, family pressure, and the passage of time.', 'https://www.youtube.com/embed/BjJcYdEOI0k', 'https://image.tmdb.org/t/p/original/lfA4GW15zJd7Xn4pWkFMyMUDOyi.jpg', '2026-08-30', 0, '2026-08-14 04:17:22'),
(11, 'Me Before You', 'Romance', 'English', 'Hollywood', '4k', 145, 'A young woman forms an unexpected bond with a wealthy man whose life has changed dramatically after an accident.', 'https://www.youtube.com/embed/T0MmkG_nG1U', 'https://m.media-amazon.com/images/M/MV5BMTQ2NjE4NDE2NV5BMl5BanBnXkFtZTgwOTcwNDE5NzE@._V1_FMjpg_UX1000_.jpg', '2026-09-21', 0, '2026-08-14 04:17:22'),
(12, 'The Fault in Our Stars', 'Romance', 'English', 'Hollywood', '4k', 109, 'Two teenagers with serious illnesses meet in a support group and develop a deep connection that changes their lives.', 'https://www.youtube.com/embed/9ItBvH5J6ss', 'https://tse4.mm.bing.net/th/id/OIP.2s_rvJ3rylxjwoKrjeAq7AHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-11-08', 1, '2026-08-14 04:17:22'),
(13, 'Crazy Rich Asians', 'Romance', 'English', 'Bollywood', 'IMAX', 146, 'A young woman discovers that her boyfriend comes from one of Singapore&#039;s wealthiest families and faces the pressures of his world.', 'https://www.youtube.com/embed/PZpkI5Oku_s', 'https://tse2.mm.bing.net/th/id/OIP.8U4h7C7cWqYYejmsidDEqQHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-12-10', 0, '2026-08-14 04:17:22'),
(14, 'Five Feet Apart', 'Romance', 'English', 'Bollywood', 'HD', 80, 'Two young patients with cystic fibrosis fall in love but must keep their distance to protect each other.', 'https://www.youtube.com/embed/I7YWtkH4nU8', 'https://tse2.mm.bing.net/th/id/OIP.8BdkmLHLA5RYZRxsHuRZ0AHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-16', 0, '2026-08-14 04:17:22'),
(15, 'Forrest Gump', 'Romance', 'English', 'Hollywood', 'HD', 101, 'A kind-hearted man with a simple outlook on life experiences extraordinary events while remaining devoted to the people he loves.', 'https://www.youtube.com/embed/Mj9IA9tTfio', 'https://tse3.mm.bing.net/th/id/OIP.Q8uXsPAH1CTHH4WKzgq6iQHaKp?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-03', 0, '2026-08-14 04:17:22'),
(16, 'Distant Tide', 'Romance', 'English', NULL, NULL, 148, 'Distant Tide is a story about timing, and what happens when it is finally right. It is a story about choosing someone, and choosing to keep choosing them. Old wounds, bad timing and second-guessing get in the way, but the pull between them does not fade. A warm, heartfelt watch about taking a chance on someone.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm016.jpg', '2026-08-08', 1, '2026-08-14 04:17:22'),
(17, 'Frozen Harbor', 'Romance', 'Urdu', NULL, NULL, 129, 'Frozen Harbor follows two people whose lives were not supposed to cross, until they do. What starts as something small slowly turns into something neither of them expected. Old wounds, bad timing and second-guessing get in the way, but the pull between them does not fade. It lands on an ending that feels honest rather than easy.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm017.jpg', '2026-08-25', 0, '2026-08-14 04:17:22'),
(18, 'Broken War', 'Romance', 'Hindi', NULL, NULL, 96, 'Broken War is a story about timing, and what happens when it is finally right. It is a story about choosing someone, and choosing to keep choosing them. Old wounds, bad timing and second-guessing get in the way, but the pull between them does not fade. A warm, heartfelt watch about taking a chance on someone.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm018.jpg', '2026-08-30', 0, '2026-08-14 04:17:22'),
(19, 'May Day', 'Action', 'Urdu', 'Hollywood', 'HD', 126, 'The film &quot;Mayday&quot; is a 2005 American television thriller film based on the 1979 novel by Thomas Block. The story is set in the Pacific Ocean, where a state-of-the-art supersonic passenger jet, Pacific Global Flight 52, is struck by an errant U.S. Navy missile. The missile cripples the aircraft, killing the flight crew and leaving most of the passengers dead, near death, or psychologically deranged. A handful of survivors must now achieve the impossible: to land the aircraft despite weather, intrigues, and technical problems. The film stars Aidan Quinn, Dean Cain, Kelly Hu, Michael Murphy, Charles S. Dutton, and Gail O&#039;Grady. The film aired on CBS in the United States on October 2, 2005.', 'https://youtu.be/TMv_g-I_g7Y?si=v6cFMndzEeXlG5v0', 'https://tse4.mm.bing.net/th/id/OIP.YF7v2i63csXtcGIcbLJWlwHaKk?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-18', 0, '2026-08-14 04:17:22'),
(20, 'The Conjuring', 'Horror', 'English', 'Hollywood', 'HD', 209, '&quot;The Conjuring,&quot; directed by James Wan and written by Chad Hayes and Carey W. Hayes, is the first film in The Conjuring Universe franchise. The story follows Ed and Lorraine Warren, portrayed by Patrick Wilson and Vera Farmiga, as they assist the Perron family, who experience increasingly disturbing events in their Rhode Island farmhouse in 1971. The Warrens confront a powerful dark presence, making it one of the most terrifying cases of their careers. The film is inspired by the Warrens’ real-life paranormal investigations, including cases that influenced stories like The Amityville Horror.', 'https://youtu.be/ejMMn0t58Lc?si=wjRWVCKuzxHJJlRz', 'https://tse2.mm.bing.net/th/id/OIP.yKT7wrPKZ1ZItryXpXSdfQHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-04', 0, '2026-08-14 04:17:22'),
(21, 'The Nun', 'Horror', 'Hindi', 'Hollywood', '4k', 345, '&amp;quot;The Nun&amp;quot; follows Father Burke and Sister Irene, who are sent by the Vatican to investigate the mysterious suicide of a nun at the Abbey of St. Carta in Romania. Upon their arrival, they discover that the abbey is haunted by a malevolent force, which is later revealed to be Valak, a demon that takes the form of a nun. The film is set in 1952 and delves into the dark history of the abbey, which was built during the Dark Ages by a Duke who attempted to summon demons, leading to the creation of Valak.', 'https://youtu.be/QF-oyCwaArU?si=QoMBPin7Qo1wV5Y0', 'https://tse4.mm.bing.net/th/id/OIP.tp221ydxSIRGtStjcaTpswHaK-?r=0&amp;amp;rs=1&amp;amp;pid=ImgDetMain&amp;amp;o=7&amp;amp;rm=3', '2026-09-05', 0, '2026-08-14 04:17:22'),
(23, 'Insidious', 'Horror', 'English', 'Bollywood', '4k', 248, 'Insidious — The Further Is Calling A family discovers that their son has become mysteriously trapped between the world of the living and a terrifying supernatural realm. They must uncover the truth and find a way to bring him back.', 'https://www.youtube.com/embed/jxU8FU3o75A', 'https://tse1.mm.bing.net/th/id/OIP.aP24-Pv7YNSGYSA0WK0LAgHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-17', 0, '2026-08-14 04:17:22'),
(24, 'Scream', 'Horror', 'Hindi', 'Bollywood', 'IMAX', 200, 'A group of teenagers is targeted by a mysterious masked killer who turns their lives into a terrifying game of survival.', 'https://www.youtube.com/embed/i3J6ACKQ7K0', 'https://tse2.mm.bing.net/th/id/OIP.bzfb3jfwU8rtnYZf-PyQdgHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-12-02', 0, '2026-08-14 04:17:22'),
(26, 'A Quiet Place', 'Horror', 'English', 'Hollywood', '4k', 146, 'A family struggles to survive in a world where mysterious creatures hunt anything that makes a sound.', 'https://www.youtube.com/embed/WR7cc5t7tv8', 'https://tse3.mm.bing.net/th/id/OIP.uBYeFirAoAqVZ5o_R0zuxAHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-22', 0, '2026-08-14 04:17:22'),
(27, 'The Exorcist', 'Horror', 'English', 'Hollywood', '4k', 182, 'Sacred Echo builds its dread slowly before letting things unravel completely. The tension is drawn out deliberately, so every quiet moment feels like it is hiding something. Nothing is quite as safe as it first appears, and the characters find that out the hard way. It is built to unsettle you long after the lights come back on.', 'https://www.youtube.com/embed/BU2eYAO31Cc', 'https://tse2.mm.bing.net/th/id/OIP.uOzDSqfXWn9leGQUg91FtQHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-11-08', 1, '2026-08-14 04:17:22'),
(28, 'La La Land', 'Comedy', 'English', 'Bollywood', '4k', 206, 'An aspiring actress and a jazz musician fall in love while pursuing their dreams in Los Angeles.', 'https://www.youtube.com/embed/0pdqf4P9MB8', 'https://tse4.mm.bing.net/th/id/OIP.jeX7IshOyj5QiEElerGquQHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-07', 0, '2026-08-14 04:17:22'),
(29, '10 Things I Hate About You', 'Comedy', 'Hindi', 'Bollywood', '4k', 153, 'A high-school romance becomes complicated when a determined boy tries to win over a sharp-witted girl as part of a scheme.', 'https://www.youtube.com/embed/yEmcEuS6xm4', 'https://tse1.mm.bing.net/th/id/OIF.gbcxSMUHdWHkLOyQJWutiw?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2027-01-03', 1, '2026-08-14 04:17:22'),
(31, 'The Hangover', 'Comedy', 'Hindi', 'Hollywood', '4k', 124, 'Three friends wake up after a wild bachelor party in Las Vegas with no memory of the night and must find their missing friend.', 'https://www.youtube.com/embed/tlize92ffnY', 'https://tse1.mm.bing.net/th/id/OIP.DNMCfcuZQjdSXlx5IXNziwHaE8?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-12', 0, '2026-08-14 04:17:22'),
(32, '21 Jump Street', 'Comedy', 'English', 'Bollywood', '4k', 115, 'Two rookie police officers go undercover at a high school to investigate a dangerous drug operation.', 'https://www.youtube.com/embed/Oj55KinxZx4', 'https://tse2.mm.bing.net/th/id/OIP.1Jm74EwiwAnybVsRO0ESxAHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-20', 0, '2026-08-14 04:17:22'),
(33, '22 Jump Street', 'Comedy', 'English', 'Hollywood', '4k', 144, 'Two undercover officers return to college to investigate a drug network while struggling with their friendship and the mission.', 'https://www.youtube.com/embed/qP755JkDxyM', 'https://tse1.explicit.bing.net/th/id/OIP.75LYQxCQc_zldRoFMbbOvQHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-03', 0, '2026-08-14 04:17:22'),
(34, 'Home Alone', 'Comedy', 'English', 'Hollywood', 'HD', 106, 'A young boy is accidentally left home alone during the holidays and must defend his house from two determined burglars.', 'https://www.youtube.com/embed/jEDaVHmw7r4', 'https://th.bing.com/th/id/R.cc7952e684d6d5dfc567e43f800d84f4?rik=mwe%2bIrXpiuWB3g&amp;pid=ImgRaw&amp;r=0', '2026-11-10', 1, '2026-08-14 04:17:22'),
(35, 'Rush Hour', 'Comedy', 'Hindi', 'Bollywood', '4k', 123, 'A Hong Kong detective and a reckless Los Angeles cop are forced to work together to rescue a kidnapped girl.', 'https://www.youtube.com/embed/JMiFsFQcFLE', 'https://tse2.mm.bing.net/th/id/OIP.esxJpOI_FVEIlKJ0wEhGFgHaLr?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-20', 0, '2026-08-14 04:17:22'),
(36, 'Superbad', 'Comedy', 'Hindi', 'Bollywood', 'HD', 148, 'Two high-school friends attempt to make the most of their final days before graduation during one chaotic night.', 'https://www.youtube.com/embed/FqNTjs4b4_c', 'https://tse2.mm.bing.net/th/id/OIP.wjg2G1fu1N2ef5KggyIF6gHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-11', 0, '2026-08-14 04:17:22'),
(37, 'Titanic', 'Sci-Fi', 'Urdu', 'Hollywood', 'HD', 200, 'A young couple from different social worlds falls in love aboard the ill-fated Titanic as disaster approaches.', 'https://www.youtube.com/embed/kVrqfYjkTdQ', 'https://tse1.mm.bing.net/th/id/OIP.O9_9ayxxf4p4aSXL8B3heQHaFj?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-23', 1, '2026-08-14 04:17:22'),
(38, 'Twilight', 'Sci-Fi', 'English', 'Bollywood', 'HD', 140, 'A teenage girl is drawn into a dangerous romance after discovering that the mysterious boy she loves is a vampire.', 'https://www.youtube.com/embed/uxjNDE2fMjI', 'https://tse2.mm.bing.net/th/id/OIP.LG0AMFwgLKrLqi-hrUboAgHaJe?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-17', 0, '2026-08-14 04:17:22'),
(39, 'The Matrix', 'Sci-Fi', 'Hindi', 'Hollywood', 'HD', 147, 'A computer hacker discovers that the reality he knows is an artificial world controlled by machines.', 'https://www.youtube.com/embed/vKQi3bBA1y8', 'https://tse1.mm.bing.net/th/id/OIP.6p6gwW5M7RMPkGiSus6YPgHaEc?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-01', 0, '2026-08-14 04:17:22'),
(40, 'Endless Shadows', 'Sci-Fi', 'English', NULL, NULL, 117, 'Endless Shadows uses its setting to ask a question that feels surprisingly close to home. Big concepts sit right next to a very personal, human story at its core. What starts as one problem quickly reveals something much bigger underneath. A story that is as much about ideas as it is about the people living through them.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm040.jpg', '2026-08-22', 0, '2026-08-14 04:17:22'),
(41, 'Rising Vow', 'Sci-Fi', 'Urdu', NULL, NULL, 106, 'Rising Vow builds a world just far enough from our own to feel both strange and familiar. The world-building is layered in gradually, letting the bigger ideas sink in alongside the story. What starts as one problem quickly reveals something much bigger underneath. It leaves you with as many questions as answers, in the best way.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm041.jpg', '2026-08-27', 0, '2026-08-14 04:17:22'),
(42, 'Hidden Promise', 'Sci-Fi', 'Hindi', NULL, NULL, 161, 'Hidden Promise uses its setting to ask a question that feels surprisingly close to home. Big concepts sit right next to a very personal, human story at its core. What starts as one problem quickly reveals something much bigger underneath. A story that is as much about ideas as it is about the people living through them.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm042.jpg', '2026-09-10', 0, '2026-08-14 04:17:22'),
(43, 'Hidden Reckoning', 'Sci-Fi', 'English', NULL, NULL, 97, 'Hidden Reckoning builds a world just far enough from our own to feel both strange and familiar. The world-building is layered in gradually, letting the bigger ideas sink in alongside the story. What starts as one problem quickly reveals something much bigger underneath. It leaves you with as many questions as answers, in the best way.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm043.jpg', '2026-09-03', 0, '2026-08-14 04:17:22'),
(44, 'Rising Tide', 'Sci-Fi', 'Hindi', NULL, NULL, 99, 'Rising Tide builds a world just far enough from our own to feel both strange and familiar. The world-building is layered in gradually, letting the bigger ideas sink in alongside the story. What starts as one problem quickly reveals something much bigger underneath. It leaves you with as many questions as answers, in the best way.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm044.jpg', '2026-08-05', 0, '2026-08-14 04:17:22'),
(45, 'Sacred Harbor', 'Sci-Fi', 'English', NULL, NULL, 156, 'Sacred Harbor uses its setting to ask a question that feels surprisingly close to home. Big concepts sit right next to a very personal, human story at its core. What starts as one problem quickly reveals something much bigger underneath. A story that is as much about ideas as it is about the people living through them.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm045.jpg', '2026-08-08', 0, '2026-08-14 04:17:22'),
(46, 'Inception', 'Thriller', 'English', 'Hollywood', 'HD', 112, 'A skilled thief who enters people&#039;s dreams is offered a chance to erase his past by planting an idea inside a target&#039;s mind.', 'https://www.youtube.com/embed/YoHD9XEInc0', 'https://tse2.mm.bing.net/th/id/OIP.vnJImFIy1GEoBBAjyZ-tfQHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-08', 0, '2026-08-14 04:17:22'),
(47, 'Joker', 'Thriller', 'Hindi', 'Hollywood', 'HD', 100, 'A troubled man in Gotham descends into chaos as society&#039;s rejection and his own struggles transform him into the infamous Joker.', 'https://www.youtube.com/embed/t433PEQGErc', 'https://tse1.mm.bing.net/th/id/OIP.ySiAG-EFWTO6xq_TIA9k-QHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-20', 0, '2026-08-14 04:17:22'),
(48, 'Se7en', 'Thriller', 'English', 'Bollywood', 'IMAX', 138, 'Two detectives investigate a series of disturbing murders inspired by the seven deadly sins.', 'https://www.youtube.com/embed/KPOuJGkpblk', 'https://tse3.mm.bing.net/th/id/OIP.EWnNstOzESeo1cVAY8wpegHaLH?r=0&amp;amp;rs=1&amp;amp;pid=ImgDetMain&amp;amp;o=7&amp;amp;rm=3', '2026-08-26', 0, '2026-08-14 04:17:22'),
(49, 'Gone Girl', 'Thriller', 'English', 'Bollywood', '4k', 104, 'A man&#039;s life unravels when his wife disappears and the investigation turns suspicion toward him.', 'https://www.youtube.com/embed/cKYibT5J87w', 'https://cinefilms-planet.fr/wp-content/uploads/2019/02/affiche-gone-girl-01-scaled-1.jpg', '2026-08-30', 0, '2026-08-14 04:17:22'),
(50, 'Shutter Island', 'Thriller', 'English', 'Hollywood', 'HD', 143, 'A U.S. marshal investigates the disappearance of a patient from an isolated psychiatric facility and begins questioning reality itself.', 'https://www.youtube.com/embed/5iaYLCiq5RM', 'https://tse1.mm.bing.net/th/id/OIP.PsrAEX68UaRgzh7oyMb3ugHaK9?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-28', 0, '2026-08-14 04:17:22'),
(51, 'Prisoners', 'Thriller', 'English', 'Bollywood', 'HD', 105, 'When two young girls disappear, a desperate father takes matters into his own hands while detectives race to uncover the truth.', 'https://www.youtube.com/embed/1kWeHtI63jc', 'https://tse4.mm.bing.net/th/id/OIP.fsC41h2G0WFhPGn66meoFwHaKe?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-09', 0, '2026-08-14 04:17:22'),
(52, 'Now You See Me', 'Thriller', 'English', 'Bollywood', '4k', 148, 'A team of illusionists uses spectacular magic tricks to pull off impossible heists while investigators try to expose them.https://www.youtube.com/results?search_query=Now+You+See+Me+official+trailer', 'https://www.youtube.com/embed/-E3lMRx7HRQ', 'https://tse1.mm.bing.net/th/id/OIP.7btM9ee_gZxWGoRgqZiS2QHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-19', 0, '2026-08-14 04:17:22'),
(53, 'Parasite', 'Thriller', 'English', 'Bollywood', '4k', 151, 'A struggling family gradually enters the lives of a wealthy household, leading to a dangerous clash between two very different worlds.', 'https://www.youtube.com/embed/isOGD_7hNIY', 'https://i.ytimg.com/vi/isOGD_7hNIY/maxresdefault.jpg', '2026-09-02', 0, '2026-08-14 04:17:22'),
(54, 'John Wick', 'Thriller', 'English', 'Hollywood', 'HD', 134, 'A legendary retired hitman is pulled back into the criminal underworld after a personal tragedy brings him face-to-face with ruthless enemies.', 'https://www.youtube.com/embed/C0BMx-qxsP4', 'https://tse2.mm.bing.net/th/id/OIP.PFlpNap32X7LhFIA7AsjGgHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-11', 0, '2026-08-14 04:17:22'),
(55, 'Spider-Man: Into the Spider-Verse', 'Animation', 'English', 'Other', 'IMAX', 124, 'A teenager becomes Spider-Man and discovers a multiverse filled with different versions of the iconic hero.', 'https://www.youtube.com/embed/g4Hbz2jLxvQ', 'https://tse2.mm.bing.net/th/id/OIP.q60S5SG7-0XKAWzWdHUMRgHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-17', 0, '2026-08-14 04:17:22'),
(56, 'Spider-Man: Across the Spider-Verse', 'Animation', 'English', 'Other', 'IMAX', 127, 'Miles Morales travels across the multiverse and faces difficult choices as he encounters a powerful Spider Society.', 'https://www.youtube.com/embed/cqGjhVJWtEg', 'https://tse4.mm.bing.net/th/id/OIP.N7LXprOjNxxuK2yopIwhegHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-04', 0, '2026-08-14 04:17:22'),
(57, 'Toy Story', 'Animation', 'English', 'Hollywood', 'HD', 120, 'A group of toys comes to life when humans are away, and Woody must adjust when a new space-ranger toy arrives.', 'https://www.youtube.com/embed/v-PjgYDrg70', 'https://tse3.mm.bing.net/th/id/OIP.nGkNoUIkCqGA6Zc94MpzEQHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-06', 0, '2026-08-14 04:17:22'),
(58, 'Frozen', 'Animation', 'English', 'Hollywood', 'HD', 151, 'A fearless young woman sets out to find her sister after magical powers plunge their kingdom into an endless winter.', 'https://www.youtube.com/embed/FLzfXQSPBOg', 'https://tse2.mm.bing.net/th/id/OIP.SkQbhhuLv8iAzg72b5keRwHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-04', 0, '2026-08-14 04:17:22'),
(59, 'The Lion King', 'Animation', 'English', 'Hollywood', 'HD', 127, 'A young lion must overcome loss and fear to reclaim his place as king of the Pride Lands.', 'https://www.youtube.com/embed/7TavVZMewpY', 'https://tse2.mm.bing.net/th/id/OIP.-utPw87WE0dxRBlZ9D-ofwHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-16', 0, '2026-08-14 04:17:22'),
(60, 'Coco', 'Animation', 'English', 'Bollywood', '4k', 122, 'A young aspiring musician enters the Land of the Dead and discovers the hidden story of his family&#039;s past.', 'https://www.youtube.com/embed/xlnPHQ3TLX8', 'https://tse2.mm.bing.net/th/id/OIP.u3UJmIrbpSZSnx9SL3bzngHaLe?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-28', 0, '2026-08-14 04:17:22'),
(61, 'Shrek', 'Animation', 'Hindi', 'Bollywood', '4k', 121, 'A grumpy ogre&#039;s quiet life is interrupted when fairy-tale creatures invade his swamp, sending him on an unexpected adventure.', 'https://www.youtube.com/embed/CwXOrWvPBPk', 'https://tse1.mm.bing.net/th/id/OIP.0EufAek7-4LK_6eiqYAnBAHaEo?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-12', 1, '2026-08-14 04:17:22'),
(62, 'Kung Fu Panda', 'Animation', 'Hindi', 'Bollywood', '4k', 100, 'A clumsy panda unexpectedly becomes a kung fu hero and must learn to believe in himself.', 'https://www.youtube.com/embed/MYy7oGQiSqI', 'https://tse1.mm.bing.net/th/id/OIP.I3WjSJUkXALTHu9l3JJxzwHaKc?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-18', 0, '2026-08-14 04:17:22'),
(63, 'How to Train Your Dragon', 'Animation', 'English', 'Bollywood', '4k', 109, 'A young Viking forms an unlikely friendship with a dragon and changes the way his village sees the creatures it fears.', 'https://www.youtube.com/embed/YqXfPATcy7s', 'https://tse2.mm.bing.net/th/id/OIP.YwfgRJT7000cmWW3xo_dlgHaEK?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-01', 0, '2026-08-14 04:17:22'),
(65, 'The Green Mile', 'Drama', 'English', 'Hollywood', 'HD', 135, 'A prison guard encounters a mysterious inmate whose extraordinary abilities challenge everything he believes about life and death.', 'https://www.youtube.com/embed/Ki4haFrqSrw', 'https://tse3.mm.bing.net/th/id/OIP.oD1NgHNiVggrUg5c33R7TgHaLG?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-18', 0, '2026-08-14 04:17:22'),
(66, 'The Pursuit of Happyness', 'Drama', 'Hindi', 'Bollywood', '4k', 104, 'A struggling father faces homelessness and hardship while fighting for a better future for himself and his young son.', 'https://www.youtube.com/embed/DMOBlEcRuw8', 'https://tse1.mm.bing.net/th/id/OIP.kaEoQUf4pr0EZj-2DVrLdAHaHZ?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-06', 1, '2026-08-14 04:17:22'),
(67, 'Downton Abbey', 'Drama', 'English', 'Bollywood', '4k', 130, 'A British period drama television series set in the early 20th century, focusing on the aristocratic Crawley family and their servants. It explores themes of social change, love, and family dynamics.', 'https://www.youtube.com/embed/Tv-ef7vjBKo', 'https://tse2.mm.bing.net/th/id/OIP.sRLcvQPoaEyOWxfzxl7hWAHaFj?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-09', 0, '2026-08-14 04:17:22'),
(68, 'Velvet Reckoning', 'Drama', 'English', NULL, NULL, 112, 'Velvet Reckoning takes its time, letting its characters and their choices carry the story. The performances do a lot of the heavy lifting, grounding the story in something real. Relationships are tested, secrets surface, and every decision carries real weight. It is the kind of story that stays with you well after it is over.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm068.jpg', '2026-08-29', 0, '2026-08-14 04:17:22'),
(70, 'Restless Journey', 'Drama', 'English', 'Hollywood', 'HD', 109, 'Restless Journey is a quieter, more personal story about the people at its center. Relationships are tested, secrets surface, and every decision carries real weight. It is less about big moments and more about the small ones that quietly change everything. It builds to an ending that feels earned rather than forced.', 'https://www.youtube.com/embed/FKSdXH89jbo', 'm070.jpg', '2026-09-13', 0, '2026-08-14 04:17:22'),
(72, 'The Shawshank Redemption', 'Drama', 'English', 'Hollywood', 'IMAX', 127, 'A banker sentenced to life in prison builds an extraordinary friendship and quietly holds onto hope for freedom.', 'https://www.youtube.com/embed/NmzuHjWmXOc', 'https://th.bing.com/th/id/R.c56739cb83700d1868d57e0d34798034?rik=p4F0ewBkU%2bX3BA&amp;pid=ImgRaw&amp;r=0', '2026-11-09', 1, '2026-08-14 04:17:22'),
(73, 'Interstellar', 'Adventure', 'English', 'Hollywood', 'HD', 163, 'A team of explorers travels through a wormhole in space in search of a new home for humanity.', 'https://www.youtube.com/embed/Lm8p5rlrSkY', 'https://tse1.mm.bing.net/th/id/OIP.V9nPc_TSQsPGD1mw_78L4AHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-18', 0, '2026-08-14 04:17:22'),
(74, 'Avengers: Endgame', 'Adventure', 'English', 'Hollywood', 'HD', 215, 'The surviving Avengers make a final attempt to reverse the devastation caused by Thanos and restore what was lost.', 'https://www.youtube.com/embed/TcMBFSGVi1c', 'https://tse1.mm.bing.net/th/id/OIP.j4hka58bWo-Mf0-gI-Ph9AHaJ4?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-27', 0, '2026-08-14 04:17:22'),
(75, 'Avengers: Infinity War', 'Adventure', 'English', 'Bollywood', '4k', 145, 'The Avengers and their allies face Thanos as he seeks the Infinity Stones to reshape the universe.', 'https://www.youtube.com/embed/QwievZ1Tx-8', 'https://tse1.mm.bing.net/th/id/OIP.WtM0wlOQjdf1Rc6kTzhReQHaK0?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-23', 0, '2026-08-14 04:17:22'),
(76, 'Iron Man', 'Adventure', 'English', 'Bollywood', '4k', 147, 'A billionaire weapons inventor builds a powerful armored suit and transforms himself into the superhero Iron Man.', 'https://www.youtube.com/embed/_xu02V1kM1w', 'https://tse1.mm.bing.net/th/id/OIP.Y8hIhu-bqg_sr39FNDOkXgHaKs?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-29', 0, '2026-08-14 04:17:22'),
(77, 'Guardians of the Galaxy', 'Adventure', 'English', 'Hollywood', '4k', 153, 'A group of unlikely heroes must unite to protect a powerful cosmic artifact from a ruthless enemy.', 'https://www.youtube.com/embed/u3V5KDHRQvk', 'https://tse1.mm.bing.net/th/id/OIP.l7xBgv-_GONYVTQkiHTvPQHaK-?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-06', 1, '2026-08-14 04:17:22'),
(91, 'Distant Journey', 'Adventure', 'English', 'Hollywood', 'HD', 120, 'An adventure-filled journey that takes its characters far from home, testing their courage and bonds along the way.', 'https://www.youtube.com/embed/GsiU3uweE4I', 'https://tse3.mm.bing.net/th/id/OIP.qce-Jwd4ASmhppXoIxN9OwHaLB?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-16', 0, '2026-08-16 19:00:36'),
(93, 'Frozen Truth', 'Comedy', 'English', 'Hollywood', 'HD', 105, 'A lighthearted comedy where hidden truths thaw out in the most unexpected and hilarious ways.', 'https://www.youtube.com/embed/LzhQO8I2erY', 'https://tse2.mm.bing.net/th/id/OIP.QKHuxj-ImUDX-ilVJo3pSAHaK0?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-02', 0, '2026-08-16 19:01:13'),
(94, 'Midnight Storm', 'Drama', 'English', 'Hollywood', '4k', 115, 'A gripping drama set against a turbulent midnight storm, where secrets surface and relationships are tested.', 'https://www.youtube.com/embed/GZno_ZyWfoM', 'https://tse1.mm.bing.net/th/id/OIP.crEONZIhNr9ZzjoYPOqIvwHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-09-16', 0, '2026-08-16 19:01:27'),
(95, 'Crimson Flame', 'Horror', 'English', 'Hollywood', 'HD', 110, 'A chilling horror story where a crimson flame ignites terror that no one who witnesses it can escape.', 'https://www.youtube.com/embed/oquZifON8Eg', 'https://tse1.mm.bing.net/th/id/OIP.tdUo-q5YGKwQIVpZ-ZY0aAHaLH?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-10-16', 0, '2026-08-16 19:01:41'),
(96, 'Shattered Whisper', 'Crime', 'English', 'Hollywood', 'HD', 112, 'A tense crime thriller where a single shattered whisper unravels a web of secrets and betrayal.', 'https://www.youtube.com/embed/WNP_1KBWqUE', 'https://tse4.mm.bing.net/th/id/OIP.9qJ916waM5tU4PPa31hEkgHaE6?r=0&amp;rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', '2026-08-29', 1, '2026-08-16 19:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `screen_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL DEFAULT 'Screen 1',
  `rows_count` int(11) NOT NULL DEFAULT 8,
  `seats_per_row` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`screen_id`, `theater_id`, `screen_name`, `rows_count`, `seats_per_row`, `created_at`) VALUES
(1, 9, 'HD', 10, 30, '2026-08-18 07:17:54'),
(2, 22, 'IMAX', 8, 10, '2026-08-18 08:00:33');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `screen_id` int(11) NOT NULL,
  `seat_row` varchar(2) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `seat_code` varchar(10) NOT NULL,
  `seat_class` enum('Gold','Platinum','Box') NOT NULL DEFAULT 'Gold'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `screen_id`, `seat_row`, `seat_number`, `seat_code`, `seat_class`) VALUES
(1, 1, 'A', 1, 'A1', 'Gold'),
(2, 1, 'A', 2, 'A2', 'Gold'),
(3, 1, 'A', 3, 'A3', 'Gold'),
(4, 1, 'A', 4, 'A4', 'Gold'),
(5, 1, 'A', 5, 'A5', 'Gold'),
(6, 1, 'A', 6, 'A6', 'Gold'),
(7, 1, 'A', 7, 'A7', 'Gold'),
(8, 1, 'A', 8, 'A8', 'Gold'),
(9, 1, 'A', 9, 'A9', 'Gold'),
(10, 1, 'A', 10, 'A10', 'Gold'),
(11, 1, 'A', 11, 'A11', 'Gold'),
(12, 1, 'A', 12, 'A12', 'Gold'),
(13, 1, 'A', 13, 'A13', 'Gold'),
(14, 1, 'A', 14, 'A14', 'Gold'),
(15, 1, 'A', 15, 'A15', 'Gold'),
(16, 1, 'A', 16, 'A16', 'Gold'),
(17, 1, 'A', 17, 'A17', 'Gold'),
(18, 1, 'A', 18, 'A18', 'Gold'),
(19, 1, 'A', 19, 'A19', 'Gold'),
(20, 1, 'A', 20, 'A20', 'Gold'),
(21, 1, 'A', 21, 'A21', 'Gold'),
(22, 1, 'A', 22, 'A22', 'Gold'),
(23, 1, 'A', 23, 'A23', 'Gold'),
(24, 1, 'A', 24, 'A24', 'Gold'),
(25, 1, 'A', 25, 'A25', 'Gold'),
(26, 1, 'A', 26, 'A26', 'Gold'),
(27, 1, 'A', 27, 'A27', 'Gold'),
(28, 1, 'A', 28, 'A28', 'Gold'),
(29, 1, 'A', 29, 'A29', 'Gold'),
(30, 1, 'A', 30, 'A30', 'Gold'),
(31, 1, 'B', 1, 'B1', 'Gold'),
(32, 1, 'B', 2, 'B2', 'Gold'),
(33, 1, 'B', 3, 'B3', 'Gold'),
(34, 1, 'B', 4, 'B4', 'Gold'),
(35, 1, 'B', 5, 'B5', 'Gold'),
(36, 1, 'B', 6, 'B6', 'Gold'),
(37, 1, 'B', 7, 'B7', 'Gold'),
(38, 1, 'B', 8, 'B8', 'Gold'),
(39, 1, 'B', 9, 'B9', 'Gold'),
(40, 1, 'B', 10, 'B10', 'Gold'),
(41, 1, 'B', 11, 'B11', 'Gold'),
(42, 1, 'B', 12, 'B12', 'Gold'),
(43, 1, 'B', 13, 'B13', 'Gold'),
(44, 1, 'B', 14, 'B14', 'Gold'),
(45, 1, 'B', 15, 'B15', 'Gold'),
(46, 1, 'B', 16, 'B16', 'Gold'),
(47, 1, 'B', 17, 'B17', 'Gold'),
(48, 1, 'B', 18, 'B18', 'Gold'),
(49, 1, 'B', 19, 'B19', 'Gold'),
(50, 1, 'B', 20, 'B20', 'Gold'),
(51, 1, 'B', 21, 'B21', 'Gold'),
(52, 1, 'B', 22, 'B22', 'Gold'),
(53, 1, 'B', 23, 'B23', 'Gold'),
(54, 1, 'B', 24, 'B24', 'Gold'),
(55, 1, 'B', 25, 'B25', 'Gold'),
(56, 1, 'B', 26, 'B26', 'Gold'),
(57, 1, 'B', 27, 'B27', 'Gold'),
(58, 1, 'B', 28, 'B28', 'Gold'),
(59, 1, 'B', 29, 'B29', 'Gold'),
(60, 1, 'B', 30, 'B30', 'Gold'),
(61, 1, 'C', 1, 'C1', 'Gold'),
(62, 1, 'C', 2, 'C2', 'Gold'),
(63, 1, 'C', 3, 'C3', 'Gold'),
(64, 1, 'C', 4, 'C4', 'Gold'),
(65, 1, 'C', 5, 'C5', 'Gold'),
(66, 1, 'C', 6, 'C6', 'Gold'),
(67, 1, 'C', 7, 'C7', 'Gold'),
(68, 1, 'C', 8, 'C8', 'Gold'),
(69, 1, 'C', 9, 'C9', 'Gold'),
(70, 1, 'C', 10, 'C10', 'Gold'),
(71, 1, 'C', 11, 'C11', 'Gold'),
(72, 1, 'C', 12, 'C12', 'Gold'),
(73, 1, 'C', 13, 'C13', 'Gold'),
(74, 1, 'C', 14, 'C14', 'Gold'),
(75, 1, 'C', 15, 'C15', 'Gold'),
(76, 1, 'C', 16, 'C16', 'Gold'),
(77, 1, 'C', 17, 'C17', 'Gold'),
(78, 1, 'C', 18, 'C18', 'Gold'),
(79, 1, 'C', 19, 'C19', 'Gold'),
(80, 1, 'C', 20, 'C20', 'Gold'),
(81, 1, 'C', 21, 'C21', 'Gold'),
(82, 1, 'C', 22, 'C22', 'Gold'),
(83, 1, 'C', 23, 'C23', 'Gold'),
(84, 1, 'C', 24, 'C24', 'Gold'),
(85, 1, 'C', 25, 'C25', 'Gold'),
(86, 1, 'C', 26, 'C26', 'Gold'),
(87, 1, 'C', 27, 'C27', 'Gold'),
(88, 1, 'C', 28, 'C28', 'Gold'),
(89, 1, 'C', 29, 'C29', 'Gold'),
(90, 1, 'C', 30, 'C30', 'Gold'),
(91, 1, 'D', 1, 'D1', 'Gold'),
(92, 1, 'D', 2, 'D2', 'Gold'),
(93, 1, 'D', 3, 'D3', 'Gold'),
(94, 1, 'D', 4, 'D4', 'Gold'),
(95, 1, 'D', 5, 'D5', 'Gold'),
(96, 1, 'D', 6, 'D6', 'Gold'),
(97, 1, 'D', 7, 'D7', 'Gold'),
(98, 1, 'D', 8, 'D8', 'Gold'),
(99, 1, 'D', 9, 'D9', 'Gold'),
(100, 1, 'D', 10, 'D10', 'Gold'),
(101, 1, 'D', 11, 'D11', 'Gold'),
(102, 1, 'D', 12, 'D12', 'Gold'),
(103, 1, 'D', 13, 'D13', 'Gold'),
(104, 1, 'D', 14, 'D14', 'Gold'),
(105, 1, 'D', 15, 'D15', 'Gold'),
(106, 1, 'D', 16, 'D16', 'Gold'),
(107, 1, 'D', 17, 'D17', 'Gold'),
(108, 1, 'D', 18, 'D18', 'Gold'),
(109, 1, 'D', 19, 'D19', 'Gold'),
(110, 1, 'D', 20, 'D20', 'Gold'),
(111, 1, 'D', 21, 'D21', 'Gold'),
(112, 1, 'D', 22, 'D22', 'Gold'),
(113, 1, 'D', 23, 'D23', 'Gold'),
(114, 1, 'D', 24, 'D24', 'Gold'),
(115, 1, 'D', 25, 'D25', 'Gold'),
(116, 1, 'D', 26, 'D26', 'Gold'),
(117, 1, 'D', 27, 'D27', 'Gold'),
(118, 1, 'D', 28, 'D28', 'Gold'),
(119, 1, 'D', 29, 'D29', 'Gold'),
(120, 1, 'D', 30, 'D30', 'Gold'),
(121, 1, 'E', 1, 'E1', 'Platinum'),
(122, 1, 'E', 2, 'E2', 'Platinum'),
(123, 1, 'E', 3, 'E3', 'Platinum'),
(124, 1, 'E', 4, 'E4', 'Platinum'),
(125, 1, 'E', 5, 'E5', 'Platinum'),
(126, 1, 'E', 6, 'E6', 'Platinum'),
(127, 1, 'E', 7, 'E7', 'Platinum'),
(128, 1, 'E', 8, 'E8', 'Platinum'),
(129, 1, 'E', 9, 'E9', 'Platinum'),
(130, 1, 'E', 10, 'E10', 'Platinum'),
(131, 1, 'E', 11, 'E11', 'Platinum'),
(132, 1, 'E', 12, 'E12', 'Platinum'),
(133, 1, 'E', 13, 'E13', 'Platinum'),
(134, 1, 'E', 14, 'E14', 'Platinum'),
(135, 1, 'E', 15, 'E15', 'Platinum'),
(136, 1, 'E', 16, 'E16', 'Platinum'),
(137, 1, 'E', 17, 'E17', 'Platinum'),
(138, 1, 'E', 18, 'E18', 'Platinum'),
(139, 1, 'E', 19, 'E19', 'Platinum'),
(140, 1, 'E', 20, 'E20', 'Platinum'),
(141, 1, 'E', 21, 'E21', 'Platinum'),
(142, 1, 'E', 22, 'E22', 'Platinum'),
(143, 1, 'E', 23, 'E23', 'Platinum'),
(144, 1, 'E', 24, 'E24', 'Platinum'),
(145, 1, 'E', 25, 'E25', 'Platinum'),
(146, 1, 'E', 26, 'E26', 'Platinum'),
(147, 1, 'E', 27, 'E27', 'Platinum'),
(148, 1, 'E', 28, 'E28', 'Platinum'),
(149, 1, 'E', 29, 'E29', 'Platinum'),
(150, 1, 'E', 30, 'E30', 'Platinum'),
(151, 1, 'F', 1, 'F1', 'Platinum'),
(152, 1, 'F', 2, 'F2', 'Platinum'),
(153, 1, 'F', 3, 'F3', 'Platinum'),
(154, 1, 'F', 4, 'F4', 'Platinum'),
(155, 1, 'F', 5, 'F5', 'Platinum'),
(156, 1, 'F', 6, 'F6', 'Platinum'),
(157, 1, 'F', 7, 'F7', 'Platinum'),
(158, 1, 'F', 8, 'F8', 'Platinum'),
(159, 1, 'F', 9, 'F9', 'Platinum'),
(160, 1, 'F', 10, 'F10', 'Platinum'),
(161, 1, 'F', 11, 'F11', 'Platinum'),
(162, 1, 'F', 12, 'F12', 'Platinum'),
(163, 1, 'F', 13, 'F13', 'Platinum'),
(164, 1, 'F', 14, 'F14', 'Platinum'),
(165, 1, 'F', 15, 'F15', 'Platinum'),
(166, 1, 'F', 16, 'F16', 'Platinum'),
(167, 1, 'F', 17, 'F17', 'Platinum'),
(168, 1, 'F', 18, 'F18', 'Platinum'),
(169, 1, 'F', 19, 'F19', 'Platinum'),
(170, 1, 'F', 20, 'F20', 'Platinum'),
(171, 1, 'F', 21, 'F21', 'Platinum'),
(172, 1, 'F', 22, 'F22', 'Platinum'),
(173, 1, 'F', 23, 'F23', 'Platinum'),
(174, 1, 'F', 24, 'F24', 'Platinum'),
(175, 1, 'F', 25, 'F25', 'Platinum'),
(176, 1, 'F', 26, 'F26', 'Platinum'),
(177, 1, 'F', 27, 'F27', 'Platinum'),
(178, 1, 'F', 28, 'F28', 'Platinum'),
(179, 1, 'F', 29, 'F29', 'Platinum'),
(180, 1, 'F', 30, 'F30', 'Platinum'),
(181, 1, 'G', 1, 'G1', 'Platinum'),
(182, 1, 'G', 2, 'G2', 'Platinum'),
(183, 1, 'G', 3, 'G3', 'Platinum'),
(184, 1, 'G', 4, 'G4', 'Platinum'),
(185, 1, 'G', 5, 'G5', 'Platinum'),
(186, 1, 'G', 6, 'G6', 'Platinum'),
(187, 1, 'G', 7, 'G7', 'Platinum'),
(188, 1, 'G', 8, 'G8', 'Platinum'),
(189, 1, 'G', 9, 'G9', 'Platinum'),
(190, 1, 'G', 10, 'G10', 'Platinum'),
(191, 1, 'G', 11, 'G11', 'Platinum'),
(192, 1, 'G', 12, 'G12', 'Platinum'),
(193, 1, 'G', 13, 'G13', 'Platinum'),
(194, 1, 'G', 14, 'G14', 'Platinum'),
(195, 1, 'G', 15, 'G15', 'Platinum'),
(196, 1, 'G', 16, 'G16', 'Platinum'),
(197, 1, 'G', 17, 'G17', 'Platinum'),
(198, 1, 'G', 18, 'G18', 'Platinum'),
(199, 1, 'G', 19, 'G19', 'Platinum'),
(200, 1, 'G', 20, 'G20', 'Platinum'),
(201, 1, 'G', 21, 'G21', 'Platinum'),
(202, 1, 'G', 22, 'G22', 'Platinum'),
(203, 1, 'G', 23, 'G23', 'Platinum'),
(204, 1, 'G', 24, 'G24', 'Platinum'),
(205, 1, 'G', 25, 'G25', 'Platinum'),
(206, 1, 'G', 26, 'G26', 'Platinum'),
(207, 1, 'G', 27, 'G27', 'Platinum'),
(208, 1, 'G', 28, 'G28', 'Platinum'),
(209, 1, 'G', 29, 'G29', 'Platinum'),
(210, 1, 'G', 30, 'G30', 'Platinum'),
(211, 1, 'H', 1, 'H1', 'Platinum'),
(212, 1, 'H', 2, 'H2', 'Platinum'),
(213, 1, 'H', 3, 'H3', 'Platinum'),
(214, 1, 'H', 4, 'H4', 'Platinum'),
(215, 1, 'H', 5, 'H5', 'Platinum'),
(216, 1, 'H', 6, 'H6', 'Platinum'),
(217, 1, 'H', 7, 'H7', 'Platinum'),
(218, 1, 'H', 8, 'H8', 'Platinum'),
(219, 1, 'H', 9, 'H9', 'Platinum'),
(220, 1, 'H', 10, 'H10', 'Platinum'),
(221, 1, 'H', 11, 'H11', 'Platinum'),
(222, 1, 'H', 12, 'H12', 'Platinum'),
(223, 1, 'H', 13, 'H13', 'Platinum'),
(224, 1, 'H', 14, 'H14', 'Platinum'),
(225, 1, 'H', 15, 'H15', 'Platinum'),
(226, 1, 'H', 16, 'H16', 'Platinum'),
(227, 1, 'H', 17, 'H17', 'Platinum'),
(228, 1, 'H', 18, 'H18', 'Platinum'),
(229, 1, 'H', 19, 'H19', 'Platinum'),
(230, 1, 'H', 20, 'H20', 'Platinum'),
(231, 1, 'H', 21, 'H21', 'Platinum'),
(232, 1, 'H', 22, 'H22', 'Platinum'),
(233, 1, 'H', 23, 'H23', 'Platinum'),
(234, 1, 'H', 24, 'H24', 'Platinum'),
(235, 1, 'H', 25, 'H25', 'Platinum'),
(236, 1, 'H', 26, 'H26', 'Platinum'),
(237, 1, 'H', 27, 'H27', 'Platinum'),
(238, 1, 'H', 28, 'H28', 'Platinum'),
(239, 1, 'H', 29, 'H29', 'Platinum'),
(240, 1, 'H', 30, 'H30', 'Platinum'),
(241, 1, 'I', 1, 'I1', 'Box'),
(242, 1, 'I', 2, 'I2', 'Box'),
(243, 1, 'I', 3, 'I3', 'Box'),
(244, 1, 'I', 4, 'I4', 'Box'),
(245, 1, 'I', 5, 'I5', 'Box'),
(246, 1, 'I', 6, 'I6', 'Box'),
(247, 1, 'I', 7, 'I7', 'Box'),
(248, 1, 'I', 8, 'I8', 'Box'),
(249, 1, 'I', 9, 'I9', 'Box'),
(250, 1, 'I', 10, 'I10', 'Box'),
(251, 1, 'I', 11, 'I11', 'Box'),
(252, 1, 'I', 12, 'I12', 'Box'),
(253, 1, 'I', 13, 'I13', 'Box'),
(254, 1, 'I', 14, 'I14', 'Box'),
(255, 1, 'I', 15, 'I15', 'Box'),
(256, 1, 'I', 16, 'I16', 'Box'),
(257, 1, 'I', 17, 'I17', 'Box'),
(258, 1, 'I', 18, 'I18', 'Box'),
(259, 1, 'I', 19, 'I19', 'Box'),
(260, 1, 'I', 20, 'I20', 'Box'),
(261, 1, 'I', 21, 'I21', 'Box'),
(262, 1, 'I', 22, 'I22', 'Box'),
(263, 1, 'I', 23, 'I23', 'Box'),
(264, 1, 'I', 24, 'I24', 'Box'),
(265, 1, 'I', 25, 'I25', 'Box'),
(266, 1, 'I', 26, 'I26', 'Box'),
(267, 1, 'I', 27, 'I27', 'Box'),
(268, 1, 'I', 28, 'I28', 'Box'),
(269, 1, 'I', 29, 'I29', 'Box'),
(270, 1, 'I', 30, 'I30', 'Box'),
(271, 1, 'J', 1, 'J1', 'Box'),
(272, 1, 'J', 2, 'J2', 'Box'),
(273, 1, 'J', 3, 'J3', 'Box'),
(274, 1, 'J', 4, 'J4', 'Box'),
(275, 1, 'J', 5, 'J5', 'Box'),
(276, 1, 'J', 6, 'J6', 'Box'),
(277, 1, 'J', 7, 'J7', 'Box'),
(278, 1, 'J', 8, 'J8', 'Box'),
(279, 1, 'J', 9, 'J9', 'Box'),
(280, 1, 'J', 10, 'J10', 'Box'),
(281, 1, 'J', 11, 'J11', 'Box'),
(282, 1, 'J', 12, 'J12', 'Box'),
(283, 1, 'J', 13, 'J13', 'Box'),
(284, 1, 'J', 14, 'J14', 'Box'),
(285, 1, 'J', 15, 'J15', 'Box'),
(286, 1, 'J', 16, 'J16', 'Box'),
(287, 1, 'J', 17, 'J17', 'Box'),
(288, 1, 'J', 18, 'J18', 'Box'),
(289, 1, 'J', 19, 'J19', 'Box'),
(290, 1, 'J', 20, 'J20', 'Box'),
(291, 1, 'J', 21, 'J21', 'Box'),
(292, 1, 'J', 22, 'J22', 'Box'),
(293, 1, 'J', 23, 'J23', 'Box'),
(294, 1, 'J', 24, 'J24', 'Box'),
(295, 1, 'J', 25, 'J25', 'Box'),
(296, 1, 'J', 26, 'J26', 'Box'),
(297, 1, 'J', 27, 'J27', 'Box'),
(298, 1, 'J', 28, 'J28', 'Box'),
(299, 1, 'J', 29, 'J29', 'Box'),
(300, 1, 'J', 30, 'J30', 'Box'),
(301, 2, 'A', 1, 'A1', 'Gold'),
(302, 2, 'A', 2, 'A2', 'Gold'),
(303, 2, 'A', 3, 'A3', 'Gold'),
(304, 2, 'A', 4, 'A4', 'Gold'),
(305, 2, 'A', 5, 'A5', 'Gold'),
(306, 2, 'A', 6, 'A6', 'Gold'),
(307, 2, 'A', 7, 'A7', 'Gold'),
(308, 2, 'A', 8, 'A8', 'Gold'),
(309, 2, 'A', 9, 'A9', 'Gold'),
(310, 2, 'A', 10, 'A10', 'Gold'),
(311, 2, 'B', 1, 'B1', 'Gold'),
(312, 2, 'B', 2, 'B2', 'Gold'),
(313, 2, 'B', 3, 'B3', 'Gold'),
(314, 2, 'B', 4, 'B4', 'Gold'),
(315, 2, 'B', 5, 'B5', 'Gold'),
(316, 2, 'B', 6, 'B6', 'Gold'),
(317, 2, 'B', 7, 'B7', 'Gold'),
(318, 2, 'B', 8, 'B8', 'Gold'),
(319, 2, 'B', 9, 'B9', 'Gold'),
(320, 2, 'B', 10, 'B10', 'Gold'),
(321, 2, 'C', 1, 'C1', 'Gold'),
(322, 2, 'C', 2, 'C2', 'Gold'),
(323, 2, 'C', 3, 'C3', 'Gold'),
(324, 2, 'C', 4, 'C4', 'Gold'),
(325, 2, 'C', 5, 'C5', 'Gold'),
(326, 2, 'C', 6, 'C6', 'Gold'),
(327, 2, 'C', 7, 'C7', 'Gold'),
(328, 2, 'C', 8, 'C8', 'Gold'),
(329, 2, 'C', 9, 'C9', 'Gold'),
(330, 2, 'C', 10, 'C10', 'Gold'),
(331, 2, 'D', 1, 'D1', 'Platinum'),
(332, 2, 'D', 2, 'D2', 'Platinum'),
(333, 2, 'D', 3, 'D3', 'Platinum'),
(334, 2, 'D', 4, 'D4', 'Platinum'),
(335, 2, 'D', 5, 'D5', 'Platinum'),
(336, 2, 'D', 6, 'D6', 'Platinum'),
(337, 2, 'D', 7, 'D7', 'Platinum'),
(338, 2, 'D', 8, 'D8', 'Platinum'),
(339, 2, 'D', 9, 'D9', 'Platinum'),
(340, 2, 'D', 10, 'D10', 'Platinum'),
(341, 2, 'E', 1, 'E1', 'Platinum'),
(342, 2, 'E', 2, 'E2', 'Platinum'),
(343, 2, 'E', 3, 'E3', 'Platinum'),
(344, 2, 'E', 4, 'E4', 'Platinum'),
(345, 2, 'E', 5, 'E5', 'Platinum'),
(346, 2, 'E', 6, 'E6', 'Platinum'),
(347, 2, 'E', 7, 'E7', 'Platinum'),
(348, 2, 'E', 8, 'E8', 'Platinum'),
(349, 2, 'E', 9, 'E9', 'Platinum'),
(350, 2, 'E', 10, 'E10', 'Platinum'),
(351, 2, 'F', 1, 'F1', 'Platinum'),
(352, 2, 'F', 2, 'F2', 'Platinum'),
(353, 2, 'F', 3, 'F3', 'Platinum'),
(354, 2, 'F', 4, 'F4', 'Platinum'),
(355, 2, 'F', 5, 'F5', 'Platinum'),
(356, 2, 'F', 6, 'F6', 'Platinum'),
(357, 2, 'F', 7, 'F7', 'Platinum'),
(358, 2, 'F', 8, 'F8', 'Platinum'),
(359, 2, 'F', 9, 'F9', 'Platinum'),
(360, 2, 'F', 10, 'F10', 'Platinum'),
(361, 2, 'G', 1, 'G1', 'Box'),
(362, 2, 'G', 2, 'G2', 'Box'),
(363, 2, 'G', 3, 'G3', 'Box'),
(364, 2, 'G', 4, 'G4', 'Box'),
(365, 2, 'G', 5, 'G5', 'Box'),
(366, 2, 'G', 6, 'G6', 'Box'),
(367, 2, 'G', 7, 'G7', 'Box'),
(368, 2, 'G', 8, 'G8', 'Box'),
(369, 2, 'G', 9, 'G9', 'Box'),
(370, 2, 'G', 10, 'G10', 'Box'),
(371, 2, 'H', 1, 'H1', 'Box'),
(372, 2, 'H', 2, 'H2', 'Box'),
(373, 2, 'H', 3, 'H3', 'Box'),
(374, 2, 'H', 4, 'H4', 'Box'),
(375, 2, 'H', 5, 'H5', 'Box'),
(376, 2, 'H', 6, 'H6', 'Box'),
(377, 2, 'H', 7, 'H7', 'Box'),
(378, 2, 'H', 8, 'H8', 'Box'),
(379, 2, 'H', 9, 'H9', 'Box'),
(380, 2, 'H', 10, 'H10', 'Box');

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `show_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `screen_id` int(11) DEFAULT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price_gold` decimal(8,2) NOT NULL,
  `price_platinum` decimal(8,2) NOT NULL,
  `price_box` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`show_id`, `movie_id`, `theater_id`, `screen_id`, `show_date`, `show_time`, `price_gold`, `price_platinum`, `price_box`) VALUES
(1, 1, 1, NULL, '2026-08-14', '22:00:00', 11256.00, 15123.00, 21229.00),
(2, 1, 3, NULL, '2026-08-14', '15:00:00', 11095.00, 15005.00, 18120.00),
(3, 1, 2, NULL, '2026-08-17', '19:45:00', 10101.00, 13317.00, 17019.00),
(4, 2, 4, NULL, '2026-08-14', '22:00:00', 12570.00, 17073.00, 23606.00),
(5, 2, 3, NULL, '2026-08-14', '15:00:00', 10144.00, 14350.00, 20373.00),
(6, 2, 2, NULL, '2026-08-17', '15:00:00', 11485.00, 15643.00, 21663.00),
(7, 3, 3, NULL, '2026-08-17', '12:30:00', 11941.00, 15304.00, 22261.00),
(8, 3, 4, NULL, '2026-08-14', '15:00:00', 10731.00, 14823.00, 20472.00),
(9, 3, 2, NULL, '2026-08-17', '12:30:00', 10337.00, 13538.00, 20028.00),
(10, 4, 3, NULL, '2026-08-17', '17:30:00', 12416.00, 16093.00, 19986.00),
(11, 4, 3, NULL, '2026-08-15', '22:00:00', 11248.00, 16080.00, 22805.00),
(12, 5, 3, NULL, '2026-08-14', '12:30:00', 12538.00, 17143.00, 21121.00),
(13, 5, 4, NULL, '2026-08-14', '17:30:00', 12234.00, 15525.00, 19347.00),
(14, 6, 2, NULL, '2026-08-16', '12:30:00', 10229.00, 14520.00, 20929.00),
(15, 6, 4, NULL, '2026-08-14', '15:00:00', 10924.00, 15331.00, 18435.00),
(16, 6, 2, NULL, '2026-08-14', '22:00:00', 11823.00, 16356.00, 20116.00),
(17, 7, 1, NULL, '2026-08-16', '19:45:00', 10791.00, 13866.00, 17487.00),
(18, 7, 3, NULL, '2026-08-16', '22:00:00', 10805.00, 15351.00, 22111.00),
(19, 7, 3, NULL, '2026-08-14', '12:30:00', 10423.00, 14562.00, 21895.00),
(20, 8, 2, NULL, '2026-08-15', '12:30:00', 11727.00, 15064.00, 18160.00),
(21, 8, 4, NULL, '2026-08-15', '19:45:00', 10676.00, 15509.00, 21889.00),
(22, 9, 2, NULL, '2026-08-17', '17:30:00', 11741.00, 15579.00, 21027.00),
(23, 9, 1, NULL, '2026-08-15', '12:30:00', 10103.00, 14395.00, 18535.00),
(24, 10, 4, NULL, '2026-08-14', '15:00:00', 12705.00, 15865.00, 21632.00),
(25, 10, 1, NULL, '2026-08-14', '22:00:00', 11928.00, 16565.00, 23248.00),
(26, 10, 3, NULL, '2026-08-14', '17:30:00', 11544.00, 14592.00, 18754.00),
(27, 11, 2, NULL, '2026-08-14', '15:00:00', 12681.00, 16601.00, 23062.00),
(28, 11, 1, NULL, '2026-08-16', '17:30:00', 11966.00, 16189.00, 23605.00),
(29, 11, 2, NULL, '2026-08-17', '22:00:00', 11219.00, 14762.00, 18469.00),
(30, 12, 1, NULL, '2026-08-16', '19:45:00', 10073.00, 14452.00, 21194.00),
(31, 12, 4, NULL, '2026-08-15', '17:30:00', 11668.00, 15383.00, 20858.00),
(32, 13, 3, NULL, '2026-08-14', '19:45:00', 10175.00, 14034.00, 20947.00),
(33, 13, 3, NULL, '2026-08-17', '17:30:00', 12830.00, 16012.00, 22157.00),
(34, 13, 2, NULL, '2026-08-15', '15:00:00', 12058.00, 15817.00, 19721.00),
(35, 14, 1, NULL, '2026-08-15', '22:00:00', 11810.00, 14825.00, 18128.00),
(36, 14, 3, NULL, '2026-08-14', '15:00:00', 12309.00, 16607.00, 23453.00),
(37, 14, 1, NULL, '2026-08-16', '12:30:00', 11129.00, 14539.00, 20506.00),
(38, 15, 1, NULL, '2026-08-14', '22:00:00', 11620.00, 15871.00, 21829.00),
(39, 15, 3, NULL, '2026-08-14', '22:00:00', 12852.00, 17192.00, 21582.00),
(40, 16, 1, NULL, '2026-08-14', '12:30:00', 10557.00, 14808.00, 18947.00),
(41, 16, 3, NULL, '2026-08-16', '17:30:00', 12333.00, 16960.00, 20348.00),
(42, 17, 1, NULL, '2026-08-15', '12:30:00', 10549.00, 14266.00, 18023.00),
(43, 17, 2, NULL, '2026-08-14', '15:00:00', 11747.00, 15395.00, 20217.00),
(44, 18, 3, NULL, '2026-08-17', '15:00:00', 12297.00, 17192.00, 22746.00),
(45, 18, 4, NULL, '2026-08-14', '17:30:00', 11556.00, 16141.00, 21683.00),
(46, 18, 1, NULL, '2026-08-16', '19:45:00', 11434.00, 15917.00, 20640.00),
(47, 19, 2, NULL, '2026-08-15', '19:45:00', 11731.00, 14850.00, 18183.00),
(48, 19, 3, NULL, '2026-08-16', '19:45:00', 10656.00, 14862.00, 21256.00),
(49, 20, 2, NULL, '2026-08-15', '12:30:00', 12747.00, 16353.00, 23002.00),
(50, 20, 2, NULL, '2026-08-16', '17:30:00', 12357.00, 16377.00, 19510.00),
(51, 20, 4, NULL, '2026-08-17', '15:00:00', 11802.00, 15191.00, 21715.00),
(52, 21, 4, NULL, '2026-08-14', '19:45:00', 12609.00, 16392.00, 21477.00),
(53, 21, 1, NULL, '2026-08-14', '19:45:00', 11599.00, 15316.00, 20551.00),
(56, 23, 3, NULL, '2026-08-14', '22:00:00', 10072.00, 14608.00, 21869.00),
(57, 23, 2, NULL, '2026-08-17', '15:00:00', 11422.00, 16025.00, 21582.00),
(58, 24, 3, NULL, '2026-08-16', '19:45:00', 12236.00, 17156.00, 24595.00),
(59, 24, 3, NULL, '2026-08-15', '12:30:00', 10901.00, 15519.00, 19772.00),
(60, 24, 1, NULL, '2026-08-14', '12:30:00', 12853.00, 17739.00, 23282.00),
(64, 26, 2, NULL, '2026-08-14', '19:45:00', 10250.00, 14758.00, 18923.00),
(65, 26, 3, NULL, '2026-08-14', '22:00:00', 11581.00, 16135.00, 19508.00),
(66, 27, 1, NULL, '2026-08-14', '15:00:00', 10596.00, 15227.00, 18341.00),
(67, 27, 3, NULL, '2026-08-17', '22:00:00', 12284.00, 17135.00, 23529.00),
(68, 27, 1, NULL, '2026-08-16', '12:30:00', 11869.00, 15536.00, 20879.00),
(69, 28, 2, NULL, '2026-08-14', '17:30:00', 10470.00, 13762.00, 18051.00),
(70, 28, 2, NULL, '2026-08-17', '12:30:00', 12513.00, 17230.00, 23735.00),
(71, 29, 2, NULL, '2026-08-17', '22:00:00', 12650.00, 16865.00, 21554.00),
(72, 29, 1, NULL, '2026-08-15', '22:00:00', 10405.00, 15090.00, 19135.00),
(75, 31, 4, NULL, '2026-08-16', '15:00:00', 10653.00, 14797.00, 19822.00),
(76, 31, 3, NULL, '2026-08-14', '22:00:00', 12427.00, 17420.00, 21208.00),
(77, 31, 2, NULL, '2026-08-14', '12:30:00', 12300.00, 16063.00, 19320.00),
(78, 32, 4, NULL, '2026-08-14', '15:00:00', 11087.00, 14802.00, 19490.00),
(79, 32, 4, NULL, '2026-08-15', '17:30:00', 10378.00, 15020.00, 21526.00),
(80, 33, 1, NULL, '2026-08-16', '22:00:00', 12729.00, 16502.00, 22584.00),
(81, 33, 1, NULL, '2026-08-14', '22:00:00', 12657.00, 17591.00, 21146.00),
(82, 33, 1, NULL, '2026-08-14', '17:30:00', 12838.00, 16138.00, 20005.00),
(83, 34, 4, NULL, '2026-08-14', '19:45:00', 11221.00, 14482.00, 21822.00),
(84, 34, 3, NULL, '2026-08-17', '22:00:00', 12446.00, 16748.00, 23941.00),
(85, 34, 4, NULL, '2026-08-14', '22:00:00', 11100.00, 14924.00, 20701.00),
(86, 35, 1, NULL, '2026-08-15', '17:30:00', 12247.00, 15413.00, 22390.00),
(87, 35, 4, NULL, '2026-08-15', '12:30:00', 11692.00, 16054.00, 19700.00),
(88, 36, 2, NULL, '2026-08-14', '12:30:00', 12497.00, 16748.00, 21611.00),
(89, 36, 3, NULL, '2026-08-16', '15:00:00', 12467.00, 15658.00, 22947.00),
(90, 36, 3, NULL, '2026-08-15', '15:00:00', 10876.00, 15283.00, 19693.00),
(91, 37, 4, NULL, '2026-08-15', '19:45:00', 11390.00, 15148.00, 22598.00),
(92, 37, 2, NULL, '2026-08-17', '19:45:00', 10210.00, 14740.00, 20813.00),
(93, 38, 1, NULL, '2026-08-14', '12:30:00', 10521.00, 13757.00, 18264.00),
(94, 38, 1, NULL, '2026-08-16', '17:30:00', 10514.00, 14241.00, 18626.00),
(95, 38, 1, NULL, '2026-08-15', '17:30:00', 11207.00, 15376.00, 22110.00),
(96, 39, 4, NULL, '2026-08-17', '22:00:00', 11155.00, 15188.00, 18490.00),
(97, 39, 2, NULL, '2026-08-14', '19:45:00', 12216.00, 16027.00, 19385.00),
(98, 40, 4, NULL, '2026-08-14', '17:30:00', 12622.00, 16521.00, 21401.00),
(99, 40, 2, NULL, '2026-08-14', '12:30:00', 11149.00, 15947.00, 20536.00),
(100, 41, 3, NULL, '2026-08-16', '22:00:00', 10012.00, 14663.00, 19869.00),
(101, 41, 3, NULL, '2026-08-16', '22:00:00', 11969.00, 15321.00, 21881.00),
(102, 42, 2, NULL, '2026-08-14', '15:00:00', 11585.00, 16578.00, 19590.00),
(103, 42, 4, NULL, '2026-08-14', '17:30:00', 10737.00, 14282.00, 19125.00),
(104, 42, 2, NULL, '2026-08-14', '12:30:00', 12611.00, 17012.00, 22421.00),
(105, 43, 4, NULL, '2026-08-14', '15:00:00', 11686.00, 15080.00, 19702.00),
(106, 43, 1, NULL, '2026-08-17', '15:00:00', 10291.00, 14766.00, 17930.00),
(107, 44, 2, NULL, '2026-08-15', '12:30:00', 12350.00, 15403.00, 22625.00),
(108, 44, 4, NULL, '2026-08-15', '22:00:00', 11983.00, 16777.00, 20431.00),
(109, 45, 3, NULL, '2026-08-14', '15:00:00', 10016.00, 13208.00, 16702.00),
(110, 45, 2, NULL, '2026-08-14', '12:30:00', 11015.00, 15949.00, 20092.00),
(111, 46, 4, NULL, '2026-08-17', '17:30:00', 12033.00, 16032.00, 23195.00),
(112, 46, 1, NULL, '2026-08-14', '22:00:00', 12728.00, 16078.00, 19135.00),
(113, 47, 4, NULL, '2026-08-14', '22:00:00', 11646.00, 15141.00, 20704.00),
(114, 47, 1, NULL, '2026-08-15', '22:00:00', 12247.00, 16981.00, 24111.00),
(115, 47, 2, NULL, '2026-08-16', '17:30:00', 10358.00, 13775.00, 19121.00),
(116, 48, 3, NULL, '2026-08-15', '15:00:00', 11723.00, 15215.00, 18496.00),
(117, 48, 1, NULL, '2026-08-14', '17:30:00', 11625.00, 15917.00, 21994.00),
(118, 48, 4, NULL, '2026-08-14', '22:00:00', 10380.00, 15219.00, 22414.00),
(119, 49, 2, NULL, '2026-08-14', '19:45:00', 12843.00, 16068.00, 23506.00),
(120, 49, 1, NULL, '2026-08-16', '22:00:00', 12721.00, 15827.00, 22760.00),
(121, 50, 1, NULL, '2026-08-16', '12:30:00', 12150.00, 16799.00, 21650.00),
(122, 50, 1, NULL, '2026-08-15', '15:00:00', 12184.00, 16678.00, 22824.00),
(123, 50, 1, NULL, '2026-08-17', '17:30:00', 10676.00, 14094.00, 19883.00),
(124, 51, 2, NULL, '2026-08-17', '15:00:00', 11525.00, 16038.00, 22959.00),
(125, 51, 2, NULL, '2026-08-16', '12:30:00', 10186.00, 13578.00, 20972.00),
(126, 52, 2, NULL, '2026-08-17', '15:00:00', 11465.00, 14766.00, 21529.00),
(127, 52, 1, NULL, '2026-08-17', '22:00:00', 12456.00, 16929.00, 23880.00),
(128, 52, 2, NULL, '2026-08-14', '22:00:00', 11843.00, 16406.00, 23844.00),
(129, 53, 1, NULL, '2026-08-14', '19:45:00', 11425.00, 15743.00, 22170.00),
(130, 53, 3, NULL, '2026-08-14', '15:00:00', 11308.00, 16276.00, 20593.00),
(131, 53, 3, NULL, '2026-08-15', '15:00:00', 10066.00, 13790.00, 20542.00),
(132, 54, 2, NULL, '2026-08-14', '12:30:00', 10677.00, 14906.00, 18201.00),
(133, 54, 1, NULL, '2026-08-15', '12:30:00', 10916.00, 14063.00, 17826.00),
(134, 54, 2, NULL, '2026-08-16', '22:00:00', 10451.00, 14154.00, 19048.00),
(135, 55, 1, NULL, '2026-08-14', '17:30:00', 11357.00, 14620.00, 19643.00),
(136, 55, 4, NULL, '2026-08-14', '17:30:00', 11116.00, 14333.00, 19913.00),
(137, 55, 4, NULL, '2026-08-14', '15:00:00', 12423.00, 16895.00, 23433.00),
(138, 56, 2, NULL, '2026-08-17', '15:00:00', 12766.00, 17027.00, 23320.00),
(139, 56, 4, NULL, '2026-08-17', '19:45:00', 11261.00, 16036.00, 20502.00),
(140, 56, 4, NULL, '2026-08-14', '19:45:00', 10504.00, 14716.00, 18468.00),
(141, 57, 1, NULL, '2026-08-15', '22:00:00', 10754.00, 13796.00, 20342.00),
(142, 57, 2, NULL, '2026-08-16', '17:30:00', 11594.00, 15233.00, 21049.00),
(143, 58, 3, NULL, '2026-08-15', '15:00:00', 12836.00, 16640.00, 20871.00),
(144, 58, 4, NULL, '2026-08-15', '19:45:00', 10948.00, 14219.00, 18171.00),
(145, 58, 4, NULL, '2026-08-14', '17:30:00', 10076.00, 13863.00, 20228.00),
(146, 59, 2, NULL, '2026-08-14', '19:45:00', 11118.00, 14284.00, 19224.00),
(147, 59, 1, NULL, '2026-08-14', '22:00:00', 12421.00, 16040.00, 22006.00),
(148, 60, 2, NULL, '2026-08-16', '17:30:00', 10471.00, 14487.00, 19413.00),
(149, 60, 4, NULL, '2026-08-16', '19:45:00', 10016.00, 14793.00, 19356.00),
(150, 61, 3, NULL, '2026-08-16', '12:30:00', 10328.00, 13793.00, 20177.00),
(151, 61, 2, NULL, '2026-08-14', '17:30:00', 12253.00, 16947.00, 20311.00),
(152, 61, 1, NULL, '2026-08-15', '12:30:00', 12100.00, 15339.00, 20055.00),
(153, 62, 2, NULL, '2026-08-16', '22:00:00', 12177.00, 15843.00, 21646.00),
(154, 62, 2, NULL, '2026-08-14', '17:30:00', 12412.00, 15435.00, 20341.00),
(155, 62, 3, NULL, '2026-08-16', '12:30:00', 11798.00, 16765.00, 23928.00),
(156, 63, 2, NULL, '2026-08-14', '12:30:00', 12113.00, 16829.00, 24131.00),
(157, 63, 4, NULL, '2026-08-17', '15:00:00', 12053.00, 16745.00, 23036.00),
(161, 65, 1, NULL, '2026-08-15', '22:00:00', 11068.00, 14611.00, 21638.00),
(162, 65, 1, NULL, '2026-08-14', '12:30:00', 11834.00, 15371.00, 22382.00),
(163, 65, 3, NULL, '2026-08-15', '22:00:00', 12780.00, 16232.00, 22102.00),
(164, 66, 2, NULL, '2026-08-17', '19:45:00', 11837.00, 16680.00, 20263.00),
(165, 66, 1, NULL, '2026-08-17', '19:45:00', 11685.00, 14777.00, 18452.00),
(166, 67, 3, NULL, '2026-08-14', '22:00:00', 12602.00, 15901.00, 21359.00),
(167, 67, 4, NULL, '2026-08-14', '12:30:00', 10064.00, 13714.00, 19147.00),
(168, 67, 1, NULL, '2026-08-16', '19:45:00', 11458.00, 15168.00, 18316.00),
(169, 68, 1, NULL, '2026-08-15', '22:00:00', 10948.00, 15782.00, 23215.00),
(170, 68, 3, NULL, '2026-08-14', '12:30:00', 12999.00, 17905.00, 21423.00),
(171, 68, 2, NULL, '2026-08-14', '22:00:00', 11018.00, 15390.00, 22799.00),
(175, 70, 2, NULL, '2026-08-16', '22:00:00', 11411.00, 15883.00, 20909.00),
(176, 70, 2, NULL, '2026-08-16', '17:30:00', 10706.00, 15346.00, 18885.00),
(179, 72, 3, NULL, '2026-08-14', '17:30:00', 10081.00, 13527.00, 18783.00),
(180, 72, 1, NULL, '2026-08-17', '15:00:00', 12018.00, 16579.00, 20962.00),
(181, 72, 1, NULL, '2026-08-17', '22:00:00', 12969.00, 17575.00, 24362.00),
(182, 73, 2, NULL, '2026-08-14', '12:30:00', 10777.00, 15674.00, 21036.00),
(183, 73, 4, NULL, '2026-08-16', '17:30:00', 12073.00, 16355.00, 23205.00),
(184, 74, 4, NULL, '2026-08-16', '17:30:00', 11260.00, 14972.00, 21221.00),
(185, 74, 1, NULL, '2026-08-14', '17:30:00', 10456.00, 14186.00, 19486.00),
(186, 74, 3, NULL, '2026-08-14', '17:30:00', 10636.00, 15500.00, 22948.00),
(187, 75, 4, NULL, '2026-08-17', '17:30:00', 11311.00, 14875.00, 22110.00),
(188, 75, 3, NULL, '2026-08-17', '22:00:00', 10667.00, 15083.00, 18843.00),
(189, 75, 4, NULL, '2026-08-14', '15:00:00', 11164.00, 15152.00, 19384.00),
(190, 76, 3, NULL, '2026-08-17', '17:30:00', 12467.00, 17317.00, 24419.00),
(191, 76, 4, NULL, '2026-08-16', '15:00:00', 12194.00, 16873.00, 22802.00),
(192, 77, 1, NULL, '2026-08-14', '12:30:00', 10184.00, 13757.00, 20468.00),
(193, 77, 2, NULL, '2026-08-14', '19:45:00', 11891.00, 15791.00, 18804.00),
(228, 9, 9, NULL, '2026-08-15', '11:00:00', 947.00, 1690.00, 3412.00),
(229, 9, 31, NULL, '2026-08-16', '14:00:00', 1110.00, 2013.00, 2521.00),
(230, 9, 5, NULL, '2026-08-17', '17:00:00', 1102.00, 1553.00, 2781.00),
(231, 9, 34, NULL, '2026-08-18', '20:00:00', 957.00, 1617.00, 2697.00),
(232, 9, 26, NULL, '2026-08-19', '22:30:00', 976.00, 1763.00, 2936.00),
(233, 9, 33, NULL, '2026-08-15', '11:00:00', 1011.00, 1645.00, 2916.00),
(234, 9, 13, NULL, '2026-08-16', '14:00:00', 921.00, 1769.00, 3021.00),
(235, 9, 3, NULL, '2026-08-17', '17:00:00', 972.00, 1637.00, 2996.00),
(236, 12, 19, NULL, '2026-08-15', '11:00:00', 833.00, 2196.00, 2715.00),
(237, 12, 26, NULL, '2026-08-16', '14:00:00', 837.00, 1793.00, 2803.00),
(238, 12, 9, NULL, '2026-08-17', '17:00:00', 950.00, 2023.00, 2521.00),
(239, 12, 11, NULL, '2026-08-18', '20:00:00', 814.00, 1989.00, 2686.00),
(240, 12, 21, NULL, '2026-08-19', '22:30:00', 1171.00, 1787.00, 2891.00),
(241, 12, 16, NULL, '2026-08-15', '11:00:00', 1066.00, 1550.00, 2730.00),
(242, 12, 6, NULL, '2026-08-16', '14:00:00', 1103.00, 1882.00, 3033.00),
(243, 12, 17, NULL, '2026-08-17', '17:00:00', 1006.00, 1807.00, 2667.00),
(244, 13, 31, NULL, '2026-08-15', '11:00:00', 1089.00, 1967.00, 3063.00),
(245, 13, 10, NULL, '2026-08-16', '14:00:00', 1111.00, 1946.00, 2718.00),
(246, 13, 4, NULL, '2026-08-17', '17:00:00', 984.00, 1890.00, 3073.00),
(247, 13, 12, NULL, '2026-08-18', '20:00:00', 880.00, 1741.00, 2671.00),
(248, 13, 25, NULL, '2026-08-19', '22:30:00', 823.00, 1610.00, 2843.00),
(249, 13, 19, NULL, '2026-08-15', '11:00:00', 884.00, 2175.00, 2904.00),
(250, 13, 28, NULL, '2026-08-16', '14:00:00', 952.00, 1915.00, 3130.00),
(251, 13, 15, NULL, '2026-08-17', '17:00:00', 1127.00, 1872.00, 2625.00),
(252, 16, 7, NULL, '2026-08-15', '11:00:00', 1152.00, 1739.00, 2954.00),
(253, 16, 21, NULL, '2026-08-16', '14:00:00', 921.00, 1909.00, 3114.00),
(254, 16, 27, NULL, '2026-08-17', '17:00:00', 929.00, 2069.00, 2681.00),
(255, 16, 5, NULL, '2026-08-18', '20:00:00', 1141.00, 1757.00, 2731.00),
(256, 16, 17, NULL, '2026-08-19', '22:30:00', 1037.00, 1766.00, 3404.00),
(257, 16, 13, NULL, '2026-08-15', '11:00:00', 1056.00, 1758.00, 2955.00),
(258, 16, 29, NULL, '2026-08-16', '14:00:00', 1176.00, 2076.00, 3440.00),
(259, 16, 14, NULL, '2026-08-17', '17:00:00', 1046.00, 1553.00, 2897.00),
(268, 27, 8, NULL, '2026-08-15', '11:00:00', 1007.00, 1924.00, 2658.00),
(269, 27, 11, NULL, '2026-08-16', '14:00:00', 823.00, 1702.00, 2998.00),
(270, 27, 9, NULL, '2026-08-17', '17:00:00', 864.00, 1630.00, 2570.00),
(271, 27, 13, NULL, '2026-08-18', '20:00:00', 1197.00, 1763.00, 2768.00),
(272, 27, 15, NULL, '2026-08-19', '22:30:00', 807.00, 1603.00, 2721.00),
(273, 27, 18, NULL, '2026-08-15', '11:00:00', 1156.00, 1779.00, 2778.00),
(274, 27, 20, NULL, '2026-08-16', '14:00:00', 1067.00, 1729.00, 3367.00),
(275, 27, 33, NULL, '2026-08-17', '17:00:00', 1192.00, 1520.00, 3412.00),
(276, 28, 34, NULL, '2026-08-15', '11:00:00', 1056.00, 1747.00, 2831.00),
(277, 28, 2, NULL, '2026-08-16', '14:00:00', 1008.00, 1746.00, 2748.00),
(278, 28, 29, NULL, '2026-08-17', '17:00:00', 1071.00, 2137.00, 2931.00),
(279, 28, 17, NULL, '2026-08-18', '20:00:00', 810.00, 2096.00, 3005.00),
(280, 28, 7, NULL, '2026-08-19', '22:30:00', 1061.00, 2155.00, 3064.00),
(281, 28, 23, NULL, '2026-08-15', '11:00:00', 940.00, 1590.00, 3081.00),
(282, 28, 12, NULL, '2026-08-16', '14:00:00', 1000.00, 2089.00, 2729.00),
(283, 28, 10, NULL, '2026-08-17', '17:00:00', 1010.00, 1584.00, 2943.00),
(284, 29, 18, NULL, '2026-08-15', '11:00:00', 891.00, 2118.00, 3484.00),
(285, 29, 32, NULL, '2026-08-16', '14:00:00', 1123.00, 2177.00, 2907.00),
(286, 29, 7, NULL, '2026-08-17', '17:00:00', 825.00, 1735.00, 3484.00),
(287, 29, 29, NULL, '2026-08-18', '20:00:00', 971.00, 2006.00, 2609.00),
(288, 29, 28, NULL, '2026-08-19', '22:30:00', 985.00, 1931.00, 2658.00),
(289, 29, 12, NULL, '2026-08-15', '11:00:00', 1042.00, 1770.00, 3357.00),
(290, 29, 6, NULL, '2026-08-16', '14:00:00', 1101.00, 1735.00, 2981.00),
(291, 29, 26, NULL, '2026-08-17', '17:00:00', 1128.00, 1681.00, 3201.00),
(300, 34, 34, NULL, '2026-08-15', '11:00:00', 1040.00, 1525.00, 2662.00),
(301, 34, 6, NULL, '2026-08-16', '14:00:00', 1082.00, 1512.00, 3360.00),
(302, 34, 10, NULL, '2026-08-17', '17:00:00', 1189.00, 1958.00, 2927.00),
(303, 34, 14, NULL, '2026-08-18', '20:00:00', 818.00, 1531.00, 2972.00),
(304, 34, 32, NULL, '2026-08-19', '22:30:00', 1173.00, 1639.00, 2758.00),
(305, 34, 16, NULL, '2026-08-15', '11:00:00', 941.00, 1957.00, 2947.00),
(306, 34, 28, NULL, '2026-08-16', '14:00:00', 925.00, 1958.00, 2939.00),
(307, 34, 5, NULL, '2026-08-17', '17:00:00', 1136.00, 1891.00, 2588.00),
(308, 37, 1, NULL, '2026-08-15', '11:00:00', 883.00, 2043.00, 3424.00),
(309, 37, 34, NULL, '2026-08-16', '14:00:00', 1160.00, 1505.00, 3193.00),
(310, 37, 12, NULL, '2026-08-17', '17:00:00', 1126.00, 1570.00, 2709.00),
(311, 37, 4, NULL, '2026-08-18', '20:00:00', 925.00, 2114.00, 3165.00),
(312, 37, 6, NULL, '2026-08-19', '22:30:00', 859.00, 2074.00, 3387.00),
(313, 37, 16, NULL, '2026-08-15', '11:00:00', 893.00, 2179.00, 2903.00),
(314, 37, 7, NULL, '2026-08-16', '14:00:00', 825.00, 1928.00, 3260.00),
(315, 37, 13, NULL, '2026-08-17', '17:00:00', 827.00, 1514.00, 2591.00),
(316, 44, 16, NULL, '2026-08-15', '11:00:00', 832.00, 2029.00, 2549.00),
(317, 44, 28, NULL, '2026-08-16', '14:00:00', 866.00, 1810.00, 2666.00),
(318, 44, 5, NULL, '2026-08-17', '17:00:00', 878.00, 1720.00, 2601.00),
(319, 44, 21, NULL, '2026-08-18', '20:00:00', 1193.00, 1685.00, 3118.00),
(320, 44, 8, NULL, '2026-08-19', '22:30:00', 872.00, 2112.00, 3032.00),
(321, 44, 29, NULL, '2026-08-15', '11:00:00', 980.00, 1998.00, 3059.00),
(322, 44, 13, NULL, '2026-08-16', '14:00:00', 1106.00, 1828.00, 2579.00),
(323, 44, 34, NULL, '2026-08-17', '17:00:00', 1070.00, 1731.00, 2803.00),
(324, 45, 3, NULL, '2026-08-15', '11:00:00', 1048.00, 1627.00, 2666.00),
(325, 45, 4, NULL, '2026-08-16', '14:00:00', 1150.00, 1747.00, 2581.00),
(326, 45, 21, NULL, '2026-08-17', '17:00:00', 1186.00, 1933.00, 2570.00),
(327, 45, 7, NULL, '2026-08-18', '20:00:00', 1058.00, 1827.00, 3118.00),
(328, 45, 1, NULL, '2026-08-19', '22:30:00', 1051.00, 2073.00, 2584.00),
(329, 45, 16, NULL, '2026-08-15', '11:00:00', 1198.00, 1677.00, 3265.00),
(330, 45, 11, NULL, '2026-08-16', '14:00:00', 813.00, 2110.00, 3034.00),
(331, 45, 32, NULL, '2026-08-17', '17:00:00', 1178.00, 1530.00, 3398.00),
(332, 46, 25, NULL, '2026-08-15', '11:00:00', 1134.00, 1661.00, 2756.00),
(333, 46, 5, NULL, '2026-08-16', '14:00:00', 954.00, 1589.00, 3468.00),
(334, 46, 4, NULL, '2026-08-17', '17:00:00', 994.00, 1879.00, 2958.00),
(335, 46, 18, NULL, '2026-08-18', '20:00:00', 966.00, 1799.00, 3343.00),
(336, 46, 16, NULL, '2026-08-19', '22:30:00', 883.00, 1718.00, 3006.00),
(337, 46, 29, NULL, '2026-08-15', '11:00:00', 1080.00, 2016.00, 2995.00),
(338, 46, 17, NULL, '2026-08-16', '14:00:00', 1030.00, 1738.00, 2538.00),
(339, 46, 11, NULL, '2026-08-17', '17:00:00', 1093.00, 1706.00, 3244.00),
(340, 51, 33, NULL, '2026-08-15', '11:00:00', 934.00, 1793.00, 3428.00),
(341, 51, 21, NULL, '2026-08-16', '14:00:00', 982.00, 2110.00, 3407.00),
(342, 51, 9, NULL, '2026-08-17', '17:00:00', 1117.00, 1795.00, 2647.00),
(343, 51, 15, NULL, '2026-08-18', '20:00:00', 1153.00, 1836.00, 3265.00),
(344, 51, 27, NULL, '2026-08-19', '22:30:00', 819.00, 2068.00, 3245.00),
(345, 51, 19, NULL, '2026-08-15', '11:00:00', 869.00, 1626.00, 2930.00),
(346, 51, 17, NULL, '2026-08-16', '14:00:00', 1104.00, 2055.00, 2999.00),
(347, 51, 20, NULL, '2026-08-17', '17:00:00', 1197.00, 2119.00, 3458.00),
(348, 56, 35, NULL, '2026-08-15', '11:00:00', 901.00, 1979.00, 3412.00),
(349, 56, 13, NULL, '2026-08-16', '14:00:00', 1037.00, 1726.00, 2805.00),
(350, 56, 18, NULL, '2026-08-17', '17:00:00', 876.00, 1839.00, 3182.00),
(351, 56, 14, NULL, '2026-08-18', '20:00:00', 1123.00, 1611.00, 3032.00),
(352, 56, 1, NULL, '2026-08-19', '22:30:00', 1162.00, 1651.00, 2745.00),
(353, 56, 32, NULL, '2026-08-15', '11:00:00', 1175.00, 2098.00, 2805.00),
(354, 56, 15, NULL, '2026-08-16', '14:00:00', 820.00, 1796.00, 2912.00),
(355, 56, 10, NULL, '2026-08-17', '17:00:00', 985.00, 2103.00, 3195.00),
(356, 59, 14, NULL, '2026-08-15', '11:00:00', 1093.00, 1902.00, 3279.00),
(357, 59, 4, NULL, '2026-08-16', '14:00:00', 1141.00, 1594.00, 2648.00),
(358, 59, 33, NULL, '2026-08-17', '17:00:00', 1151.00, 1559.00, 2524.00),
(359, 59, 11, NULL, '2026-08-18', '20:00:00', 973.00, 1895.00, 3283.00),
(360, 59, 24, NULL, '2026-08-19', '22:30:00', 931.00, 1904.00, 2679.00),
(361, 59, 2, NULL, '2026-08-15', '11:00:00', 1126.00, 1800.00, 2832.00),
(362, 59, 15, NULL, '2026-08-16', '14:00:00', 1133.00, 2068.00, 3038.00),
(363, 59, 36, NULL, '2026-08-17', '17:00:00', 1191.00, 1927.00, 3376.00),
(364, 61, 13, NULL, '2026-08-15', '11:00:00', 1120.00, 2142.00, 2935.00),
(365, 61, 25, NULL, '2026-08-16', '14:00:00', 936.00, 2144.00, 3188.00),
(366, 61, 24, NULL, '2026-08-17', '17:00:00', 870.00, 1937.00, 3182.00),
(367, 61, 9, NULL, '2026-08-18', '20:00:00', 920.00, 2142.00, 3001.00),
(368, 61, 8, NULL, '2026-08-19', '22:30:00', 1082.00, 2073.00, 2719.00),
(369, 61, 19, NULL, '2026-08-15', '11:00:00', 939.00, 1648.00, 3064.00),
(370, 61, 15, NULL, '2026-08-16', '14:00:00', 1042.00, 2029.00, 3273.00),
(371, 61, 3, NULL, '2026-08-17', '17:00:00', 945.00, 1949.00, 3147.00),
(372, 66, 32, NULL, '2026-08-15', '11:00:00', 1122.00, 1739.00, 3138.00),
(373, 66, 16, NULL, '2026-08-16', '14:00:00', 1095.00, 1537.00, 3277.00),
(374, 66, 30, NULL, '2026-08-17', '17:00:00', 852.00, 1776.00, 2812.00),
(375, 66, 33, NULL, '2026-08-18', '20:00:00', 908.00, 2190.00, 2750.00),
(376, 66, 19, NULL, '2026-08-19', '22:30:00', 1074.00, 1736.00, 2868.00),
(377, 66, 2, NULL, '2026-08-15', '11:00:00', 1016.00, 1628.00, 3152.00),
(378, 66, 8, NULL, '2026-08-16', '14:00:00', 881.00, 1537.00, 2788.00),
(379, 66, 25, NULL, '2026-08-17', '17:00:00', 1153.00, 2106.00, 3202.00),
(380, 72, 29, NULL, '2026-08-15', '11:00:00', 821.00, 2064.00, 3216.00),
(381, 72, 18, NULL, '2026-08-16', '14:00:00', 1006.00, 1997.00, 2525.00),
(382, 72, 23, NULL, '2026-08-17', '17:00:00', 861.00, 2146.00, 2566.00),
(383, 72, 35, NULL, '2026-08-18', '20:00:00', 1049.00, 1648.00, 2705.00),
(384, 72, 30, NULL, '2026-08-19', '22:30:00', 861.00, 1895.00, 3259.00),
(385, 72, 34, NULL, '2026-08-15', '11:00:00', 1103.00, 2118.00, 2953.00),
(386, 72, 31, NULL, '2026-08-16', '14:00:00', 984.00, 1643.00, 2971.00),
(387, 72, 4, NULL, '2026-08-17', '17:00:00', 947.00, 1732.00, 2655.00),
(388, 77, 12, NULL, '2026-08-15', '11:00:00', 1111.00, 2118.00, 3058.00),
(389, 77, 13, NULL, '2026-08-16', '14:00:00', 1181.00, 2016.00, 2950.00),
(390, 77, 10, NULL, '2026-08-17', '17:00:00', 1137.00, 1861.00, 3427.00),
(391, 77, 25, NULL, '2026-08-18', '20:00:00', 1032.00, 2081.00, 3305.00),
(392, 77, 29, NULL, '2026-08-19', '22:30:00', 1087.00, 1896.00, 3205.00),
(393, 77, 18, NULL, '2026-08-15', '11:00:00', 1026.00, 1768.00, 3351.00),
(394, 77, 6, NULL, '2026-08-16', '14:00:00', 969.00, 1597.00, 2727.00),
(395, 77, 2, NULL, '2026-08-17', '17:00:00', 978.00, 1627.00, 2827.00),
(412, 9, 10, NULL, '2026-08-15', '11:00:00', 898.00, 2043.00, 2733.00),
(413, 9, 24, NULL, '2026-08-16', '14:00:00', 990.00, 2023.00, 2892.00),
(414, 9, 2, NULL, '2026-08-17', '17:00:00', 883.00, 1593.00, 2505.00),
(415, 9, 33, NULL, '2026-08-18', '20:00:00', 885.00, 2029.00, 2699.00),
(416, 9, 36, NULL, '2026-08-19', '22:30:00', 1185.00, 2095.00, 2558.00),
(417, 9, 11, NULL, '2026-08-15', '11:00:00', 986.00, 1804.00, 2822.00),
(418, 9, 35, NULL, '2026-08-16', '14:00:00', 1190.00, 1825.00, 3386.00),
(419, 9, 7, NULL, '2026-08-17', '17:00:00', 837.00, 1595.00, 2623.00),
(420, 12, 26, NULL, '2026-08-15', '11:00:00', 821.00, 1901.00, 3273.00),
(421, 12, 34, NULL, '2026-08-16', '14:00:00', 1051.00, 1892.00, 3010.00),
(422, 12, 14, NULL, '2026-08-17', '17:00:00', 804.00, 1559.00, 2525.00),
(423, 12, 22, NULL, '2026-08-18', '20:00:00', 834.00, 1761.00, 3208.00),
(424, 12, 7, NULL, '2026-08-19', '22:30:00', 804.00, 1501.00, 3295.00),
(425, 12, 29, NULL, '2026-08-15', '11:00:00', 1156.00, 1631.00, 2507.00),
(426, 12, 17, NULL, '2026-08-16', '14:00:00', 992.00, 1964.00, 3295.00),
(427, 12, 8, NULL, '2026-08-17', '17:00:00', 966.00, 1591.00, 3153.00),
(428, 13, 5, NULL, '2026-08-15', '11:00:00', 849.00, 1681.00, 3350.00),
(429, 13, 25, NULL, '2026-08-16', '14:00:00', 1197.00, 1689.00, 3124.00),
(430, 13, 27, NULL, '2026-08-17', '17:00:00', 1068.00, 1980.00, 2882.00),
(431, 13, 16, NULL, '2026-08-18', '20:00:00', 978.00, 1960.00, 2834.00),
(432, 13, 29, NULL, '2026-08-19', '22:30:00', 890.00, 1586.00, 2842.00),
(433, 13, 18, NULL, '2026-08-15', '11:00:00', 926.00, 1980.00, 2878.00),
(434, 13, 35, NULL, '2026-08-16', '14:00:00', 990.00, 1558.00, 3199.00),
(435, 13, 21, NULL, '2026-08-17', '17:00:00', 901.00, 2127.00, 2937.00),
(436, 16, 32, NULL, '2026-08-15', '11:00:00', 916.00, 1645.00, 2571.00),
(437, 16, 24, NULL, '2026-08-16', '14:00:00', 868.00, 1729.00, 3057.00),
(438, 16, 36, NULL, '2026-08-17', '17:00:00', 1155.00, 1769.00, 2689.00),
(439, 16, 6, NULL, '2026-08-18', '20:00:00', 876.00, 1685.00, 3136.00),
(440, 16, 27, NULL, '2026-08-19', '22:30:00', 1017.00, 2103.00, 2834.00),
(441, 16, 13, NULL, '2026-08-15', '11:00:00', 814.00, 1720.00, 2754.00),
(442, 16, 16, NULL, '2026-08-16', '14:00:00', 1072.00, 1502.00, 2974.00),
(443, 16, 34, NULL, '2026-08-17', '17:00:00', 902.00, 1900.00, 2952.00),
(452, 28, 25, NULL, '2026-08-15', '11:00:00', 893.00, 1565.00, 3352.00),
(453, 28, 21, NULL, '2026-08-16', '14:00:00', 963.00, 2123.00, 2705.00),
(454, 28, 27, NULL, '2026-08-17', '17:00:00', 987.00, 1757.00, 3483.00),
(455, 28, 29, NULL, '2026-08-18', '20:00:00', 1027.00, 1858.00, 3454.00),
(456, 28, 18, NULL, '2026-08-19', '22:30:00', 946.00, 1819.00, 2914.00),
(457, 28, 15, NULL, '2026-08-15', '11:00:00', 897.00, 1516.00, 2527.00),
(458, 28, 34, NULL, '2026-08-16', '14:00:00', 957.00, 1925.00, 3412.00),
(459, 28, 6, NULL, '2026-08-17', '17:00:00', 1058.00, 2099.00, 2986.00),
(460, 29, 24, NULL, '2026-08-15', '11:00:00', 1064.00, 2017.00, 2879.00),
(461, 29, 14, NULL, '2026-08-16', '14:00:00', 1020.00, 1788.00, 2819.00),
(462, 29, 25, NULL, '2026-08-17', '17:00:00', 806.00, 2099.00, 2893.00),
(463, 29, 17, NULL, '2026-08-18', '20:00:00', 888.00, 2138.00, 3339.00),
(464, 29, 5, NULL, '2026-08-19', '22:30:00', 1020.00, 1951.00, 2913.00),
(465, 29, 22, NULL, '2026-08-15', '11:00:00', 807.00, 2175.00, 2657.00),
(466, 29, 8, NULL, '2026-08-16', '14:00:00', 1069.00, 2153.00, 3076.00),
(467, 29, 2, NULL, '2026-08-17', '17:00:00', 893.00, 1633.00, 3342.00),
(476, 34, 15, NULL, '2026-08-15', '11:00:00', 1120.00, 1634.00, 2550.00),
(477, 34, 1, NULL, '2026-08-16', '14:00:00', 977.00, 1783.00, 2538.00),
(478, 34, 36, NULL, '2026-08-17', '17:00:00', 851.00, 1582.00, 2521.00),
(479, 34, 34, NULL, '2026-08-18', '20:00:00', 1080.00, 1830.00, 2537.00),
(480, 34, 14, NULL, '2026-08-19', '22:30:00', 829.00, 2127.00, 3209.00),
(481, 34, 27, NULL, '2026-08-15', '11:00:00', 900.00, 1776.00, 2787.00),
(482, 34, 11, NULL, '2026-08-16', '14:00:00', 852.00, 1785.00, 3313.00),
(483, 34, 20, NULL, '2026-08-17', '17:00:00', 1106.00, 1568.00, 3034.00),
(484, 37, 24, NULL, '2026-08-15', '11:00:00', 1081.00, 1905.00, 3472.00),
(485, 37, 13, NULL, '2026-08-16', '14:00:00', 935.00, 1778.00, 2900.00),
(486, 37, 8, NULL, '2026-08-17', '17:00:00', 1125.00, 1639.00, 3466.00),
(487, 37, 20, NULL, '2026-08-18', '20:00:00', 1105.00, 1990.00, 3366.00),
(488, 37, 21, NULL, '2026-08-19', '22:30:00', 1045.00, 1637.00, 3449.00),
(489, 37, 1, NULL, '2026-08-15', '11:00:00', 869.00, 1794.00, 3152.00),
(490, 37, 4, NULL, '2026-08-16', '14:00:00', 973.00, 1848.00, 3111.00),
(491, 37, 2, NULL, '2026-08-17', '17:00:00', 804.00, 1530.00, 3178.00),
(492, 44, 10, NULL, '2026-08-15', '11:00:00', 1074.00, 2115.00, 2986.00),
(493, 44, 18, NULL, '2026-08-16', '14:00:00', 1095.00, 1957.00, 2542.00),
(494, 44, 4, NULL, '2026-08-17', '17:00:00', 1140.00, 2051.00, 3175.00),
(495, 44, 7, NULL, '2026-08-18', '20:00:00', 1024.00, 1536.00, 3420.00),
(496, 44, 2, NULL, '2026-08-19', '22:30:00', 908.00, 1687.00, 3251.00),
(497, 44, 16, NULL, '2026-08-15', '11:00:00', 1006.00, 2147.00, 2515.00),
(498, 44, 35, NULL, '2026-08-16', '14:00:00', 1014.00, 1838.00, 3170.00),
(499, 44, 27, NULL, '2026-08-17', '17:00:00', 866.00, 1870.00, 3437.00),
(500, 45, 12, NULL, '2026-08-15', '11:00:00', 951.00, 1665.00, 2648.00),
(501, 45, 5, NULL, '2026-08-16', '14:00:00', 1033.00, 1911.00, 2920.00),
(502, 45, 35, NULL, '2026-08-17', '17:00:00', 978.00, 2020.00, 3368.00),
(503, 45, 4, NULL, '2026-08-18', '20:00:00', 1132.00, 1521.00, 3197.00),
(504, 45, 24, NULL, '2026-08-19', '22:30:00', 1047.00, 1507.00, 3125.00),
(505, 45, 11, NULL, '2026-08-15', '11:00:00', 1162.00, 1864.00, 3342.00),
(506, 45, 25, NULL, '2026-08-16', '14:00:00', 828.00, 1810.00, 3431.00),
(507, 45, 16, NULL, '2026-08-17', '17:00:00', 1104.00, 2064.00, 3273.00),
(508, 46, 15, NULL, '2026-08-15', '11:00:00', 1074.00, 2056.00, 3104.00),
(509, 46, 27, NULL, '2026-08-16', '14:00:00', 1121.00, 1546.00, 3082.00),
(510, 46, 31, NULL, '2026-08-17', '17:00:00', 1085.00, 1509.00, 3416.00),
(511, 46, 6, NULL, '2026-08-18', '20:00:00', 1149.00, 2136.00, 3448.00),
(512, 46, 1, NULL, '2026-08-19', '22:30:00', 1156.00, 2067.00, 2519.00),
(513, 46, 25, NULL, '2026-08-15', '11:00:00', 805.00, 2168.00, 3431.00),
(514, 46, 28, NULL, '2026-08-16', '14:00:00', 839.00, 1788.00, 3213.00),
(515, 46, 12, NULL, '2026-08-17', '17:00:00', 925.00, 1931.00, 2851.00),
(516, 59, 15, NULL, '2026-08-15', '11:00:00', 1054.00, 1649.00, 2840.00),
(517, 59, 16, NULL, '2026-08-16', '14:00:00', 927.00, 1651.00, 2553.00),
(518, 59, 6, NULL, '2026-08-17', '17:00:00', 954.00, 1574.00, 3166.00),
(519, 59, 21, NULL, '2026-08-18', '20:00:00', 824.00, 1740.00, 3188.00),
(520, 59, 7, NULL, '2026-08-19', '22:30:00', 1080.00, 1872.00, 2592.00),
(521, 59, 23, NULL, '2026-08-15', '11:00:00', 846.00, 1817.00, 3036.00),
(522, 59, 30, NULL, '2026-08-16', '14:00:00', 1064.00, 2073.00, 3397.00),
(523, 59, 14, NULL, '2026-08-17', '17:00:00', 910.00, 1895.00, 2634.00),
(524, 61, 9, NULL, '2026-08-15', '11:00:00', 1113.00, 2156.00, 2537.00),
(525, 61, 11, NULL, '2026-08-16', '14:00:00', 955.00, 1776.00, 2891.00),
(526, 61, 7, NULL, '2026-08-17', '17:00:00', 1084.00, 1877.00, 2958.00),
(527, 61, 13, NULL, '2026-08-18', '20:00:00', 1077.00, 2169.00, 3345.00),
(528, 61, 19, NULL, '2026-08-19', '22:30:00', 1102.00, 1584.00, 3455.00),
(529, 61, 12, NULL, '2026-08-15', '11:00:00', 1132.00, 2147.00, 3072.00),
(530, 61, 27, NULL, '2026-08-16', '14:00:00', 1192.00, 1523.00, 2535.00),
(531, 61, 30, NULL, '2026-08-17', '17:00:00', 1109.00, 1622.00, 3457.00),
(532, 77, 33, NULL, '2026-08-15', '11:00:00', 1142.00, 1619.00, 3264.00),
(533, 77, 18, NULL, '2026-08-16', '14:00:00', 905.00, 1567.00, 2912.00),
(534, 77, 13, NULL, '2026-08-17', '17:00:00', 939.00, 2031.00, 2817.00),
(535, 77, 16, NULL, '2026-08-18', '20:00:00', 963.00, 2052.00, 3467.00),
(536, 77, 25, NULL, '2026-08-19', '22:30:00', 951.00, 1613.00, 2583.00),
(537, 77, 9, NULL, '2026-08-15', '11:00:00', 807.00, 1555.00, 2970.00),
(538, 77, 29, NULL, '2026-08-16', '14:00:00', 1141.00, 2162.00, 2865.00),
(539, 77, 10, NULL, '2026-08-17', '17:00:00', 821.00, 2148.00, 2775.00),
(548, 23, 20, NULL, '2026-08-16', '11:00:00', 1094.00, 1927.00, 2999.00),
(549, 23, 17, NULL, '2026-08-17', '14:00:00', 802.00, 1900.00, 3333.00),
(550, 23, 4, NULL, '2026-08-18', '17:00:00', 895.00, 1717.00, 3380.00),
(551, 23, 2, NULL, '2026-08-19', '20:00:00', 867.00, 1832.00, 2688.00),
(552, 23, 16, NULL, '2026-08-20', '22:30:00', 854.00, 2042.00, 3329.00),
(553, 23, 15, NULL, '2026-08-16', '11:00:00', 863.00, 1675.00, 3460.00),
(554, 23, 13, NULL, '2026-08-17', '14:00:00', 1133.00, 1724.00, 2581.00),
(555, 23, 29, NULL, '2026-08-18', '17:00:00', 883.00, 1830.00, 3357.00),
(556, 24, 22, NULL, '2026-08-16', '11:00:00', 1006.00, 1930.00, 2650.00),
(557, 24, 1, NULL, '2026-08-17', '14:00:00', 1182.00, 1634.00, 2707.00),
(558, 24, 29, NULL, '2026-08-18', '17:00:00', 1190.00, 1596.00, 2637.00),
(559, 24, 32, NULL, '2026-08-19', '20:00:00', 983.00, 1674.00, 3349.00),
(560, 24, 25, NULL, '2026-08-20', '22:30:00', 946.00, 1774.00, 3005.00),
(561, 24, 14, NULL, '2026-08-16', '11:00:00', 1075.00, 2132.00, 3380.00),
(562, 24, 11, NULL, '2026-08-17', '14:00:00', 981.00, 2049.00, 3385.00),
(563, 24, 34, NULL, '2026-08-18', '17:00:00', 1135.00, 1908.00, 2603.00),
(564, 27, 1, NULL, '2026-08-16', '11:00:00', 1067.00, 1623.00, 3274.00),
(565, 27, 34, NULL, '2026-08-17', '14:00:00', 970.00, 1916.00, 2756.00),
(566, 27, 30, NULL, '2026-08-18', '17:00:00', 916.00, 2173.00, 3250.00),
(567, 27, 15, NULL, '2026-08-19', '20:00:00', 831.00, 1666.00, 2894.00),
(568, 27, 28, NULL, '2026-08-20', '22:30:00', 964.00, 1581.00, 3087.00),
(569, 27, 23, NULL, '2026-08-16', '11:00:00', 1062.00, 2096.00, 2949.00),
(570, 27, 21, NULL, '2026-08-17', '14:00:00', 1057.00, 1502.00, 2814.00),
(571, 27, 7, NULL, '2026-08-18', '17:00:00', 847.00, 1969.00, 2699.00),
(572, 38, 25, NULL, '2026-08-16', '11:00:00', 1040.00, 1824.00, 3096.00),
(573, 38, 13, NULL, '2026-08-17', '14:00:00', 1119.00, 1813.00, 2650.00),
(574, 38, 12, NULL, '2026-08-18', '17:00:00', 857.00, 2198.00, 3407.00),
(575, 38, 23, NULL, '2026-08-19', '20:00:00', 1064.00, 2101.00, 2911.00),
(576, 38, 27, NULL, '2026-08-20', '22:30:00', 1112.00, 1753.00, 3196.00),
(577, 38, 31, NULL, '2026-08-16', '11:00:00', 828.00, 2103.00, 2617.00),
(578, 38, 1, NULL, '2026-08-17', '14:00:00', 921.00, 1913.00, 2890.00),
(579, 38, 8, NULL, '2026-08-18', '17:00:00', 934.00, 1984.00, 3197.00),
(580, 51, 5, NULL, '2026-08-16', '11:00:00', 992.00, 2134.00, 2852.00),
(581, 51, 32, NULL, '2026-08-17', '14:00:00', 1158.00, 1630.00, 2939.00),
(582, 51, 15, NULL, '2026-08-18', '17:00:00', 892.00, 2119.00, 2856.00),
(583, 51, 14, NULL, '2026-08-19', '20:00:00', 867.00, 1715.00, 3048.00),
(584, 51, 19, NULL, '2026-08-20', '22:30:00', 1023.00, 2177.00, 2866.00),
(585, 51, 35, NULL, '2026-08-16', '11:00:00', 916.00, 1859.00, 3231.00),
(586, 51, 16, NULL, '2026-08-17', '14:00:00', 826.00, 1561.00, 3222.00),
(587, 51, 7, NULL, '2026-08-18', '17:00:00', 938.00, 1683.00, 3344.00),
(588, 56, 35, NULL, '2026-08-16', '11:00:00', 1172.00, 2091.00, 3181.00),
(589, 56, 11, NULL, '2026-08-17', '14:00:00', 921.00, 1848.00, 2977.00),
(590, 56, 28, NULL, '2026-08-18', '17:00:00', 1125.00, 2163.00, 2752.00),
(591, 56, 24, NULL, '2026-08-19', '20:00:00', 1091.00, 1958.00, 2687.00),
(592, 56, 30, NULL, '2026-08-20', '22:30:00', 1159.00, 1852.00, 3428.00),
(593, 56, 26, NULL, '2026-08-16', '11:00:00', 1016.00, 1731.00, 3113.00),
(594, 56, 17, NULL, '2026-08-17', '14:00:00', 802.00, 1749.00, 3417.00),
(595, 56, 18, NULL, '2026-08-18', '17:00:00', 930.00, 1714.00, 2674.00),
(596, 66, 12, NULL, '2026-08-16', '11:00:00', 986.00, 1760.00, 3129.00),
(597, 66, 7, NULL, '2026-08-17', '14:00:00', 877.00, 1985.00, 2938.00),
(598, 66, 5, NULL, '2026-08-18', '17:00:00', 940.00, 1733.00, 2826.00),
(599, 66, 13, NULL, '2026-08-19', '20:00:00', 1010.00, 1544.00, 2522.00),
(600, 66, 15, NULL, '2026-08-20', '22:30:00', 854.00, 2082.00, 3042.00),
(601, 66, 28, NULL, '2026-08-16', '11:00:00', 1016.00, 2144.00, 2894.00),
(602, 66, 19, NULL, '2026-08-17', '14:00:00', 822.00, 1500.00, 3232.00),
(603, 66, 25, NULL, '2026-08-18', '17:00:00', 1123.00, 1570.00, 3173.00),
(604, 72, 10, NULL, '2026-08-16', '11:00:00', 845.00, 2036.00, 3486.00),
(605, 72, 36, NULL, '2026-08-17', '14:00:00', 846.00, 2041.00, 3235.00),
(606, 72, 23, NULL, '2026-08-18', '17:00:00', 1012.00, 1877.00, 3037.00),
(607, 72, 18, NULL, '2026-08-19', '20:00:00', 988.00, 1617.00, 2601.00),
(608, 72, 29, NULL, '2026-08-20', '22:30:00', 989.00, 1659.00, 2873.00),
(609, 72, 22, NULL, '2026-08-16', '11:00:00', 1140.00, 1701.00, 3203.00),
(610, 72, 24, NULL, '2026-08-17', '14:00:00', 1005.00, 1585.00, 3172.00),
(611, 72, 20, NULL, '2026-08-18', '17:00:00', 1118.00, 1546.00, 2823.00),
(620, 91, 10, NULL, '2026-08-16', '11:00:00', 1172.00, 2018.00, 2763.00),
(621, 91, 22, NULL, '2026-08-17', '14:00:00', 1066.00, 1610.00, 3427.00),
(622, 91, 18, NULL, '2026-08-18', '17:00:00', 1177.00, 1968.00, 3111.00),
(623, 91, 21, NULL, '2026-08-19', '20:00:00', 1098.00, 2110.00, 2730.00),
(624, 91, 7, NULL, '2026-08-20', '22:30:00', 1030.00, 1705.00, 2571.00),
(625, 91, 13, NULL, '2026-08-16', '11:00:00', 940.00, 2156.00, 2550.00),
(626, 91, 24, NULL, '2026-08-17', '14:00:00', 1197.00, 1943.00, 2681.00),
(627, 91, 29, NULL, '2026-08-18', '17:00:00', 903.00, 1887.00, 3222.00),
(636, 93, 10, NULL, '2026-08-16', '11:00:00', 956.00, 1671.00, 2990.00),
(637, 93, 1, NULL, '2026-08-17', '14:00:00', 1131.00, 2194.00, 2620.00),
(638, 93, 5, NULL, '2026-08-18', '17:00:00', 1093.00, 2124.00, 3390.00),
(639, 93, 15, NULL, '2026-08-19', '20:00:00', 1195.00, 2135.00, 2724.00),
(640, 93, 13, NULL, '2026-08-20', '22:30:00', 1136.00, 1757.00, 2551.00),
(641, 93, 25, NULL, '2026-08-16', '11:00:00', 852.00, 1725.00, 2804.00),
(642, 93, 26, NULL, '2026-08-17', '14:00:00', 902.00, 1811.00, 3081.00),
(643, 93, 35, NULL, '2026-08-18', '17:00:00', 1084.00, 1756.00, 2510.00),
(644, 94, 17, NULL, '2026-08-16', '11:00:00', 1099.00, 2053.00, 2575.00),
(645, 94, 6, NULL, '2026-08-17', '14:00:00', 977.00, 1624.00, 2586.00),
(646, 94, 16, NULL, '2026-08-18', '17:00:00', 1092.00, 1557.00, 3136.00),
(647, 94, 7, NULL, '2026-08-19', '20:00:00', 946.00, 1682.00, 3032.00),
(648, 94, 22, NULL, '2026-08-20', '22:30:00', 820.00, 1811.00, 3362.00),
(649, 94, 30, NULL, '2026-08-16', '11:00:00', 966.00, 1508.00, 3292.00),
(650, 94, 14, NULL, '2026-08-17', '14:00:00', 1059.00, 1809.00, 3157.00),
(651, 94, 15, NULL, '2026-08-18', '17:00:00', 1156.00, 2148.00, 3044.00),
(652, 95, 23, NULL, '2026-08-16', '11:00:00', 949.00, 1755.00, 3347.00),
(653, 95, 19, NULL, '2026-08-17', '14:00:00', 1003.00, 1750.00, 3002.00),
(654, 95, 30, NULL, '2026-08-18', '17:00:00', 1109.00, 1677.00, 3225.00),
(655, 95, 6, NULL, '2026-08-19', '20:00:00', 907.00, 1653.00, 2909.00),
(656, 95, 29, NULL, '2026-08-20', '22:30:00', 844.00, 1899.00, 3074.00),
(657, 95, 14, NULL, '2026-08-16', '11:00:00', 1042.00, 1783.00, 2504.00),
(658, 95, 13, NULL, '2026-08-17', '14:00:00', 1030.00, 2138.00, 3362.00),
(659, 95, 11, NULL, '2026-08-18', '17:00:00', 849.00, 2050.00, 2891.00),
(660, 96, 26, NULL, '2026-08-16', '11:00:00', 918.00, 1593.00, 2941.00),
(661, 96, 5, NULL, '2026-08-17', '14:00:00', 978.00, 1899.00, 2946.00),
(662, 96, 28, NULL, '2026-08-18', '17:00:00', 1037.00, 1650.00, 3049.00),
(663, 96, 7, NULL, '2026-08-19', '20:00:00', 1177.00, 1962.00, 2880.00),
(664, 96, 13, NULL, '2026-08-20', '22:30:00', 997.00, 1546.00, 3102.00),
(665, 96, 3, NULL, '2026-08-16', '11:00:00', 891.00, 2023.00, 2553.00),
(666, 96, 12, NULL, '2026-08-17', '14:00:00', 956.00, 1594.00, 3079.00),
(667, 96, 24, NULL, '2026-08-18', '17:00:00', 812.00, 1819.00, 2938.00),
(668, 48, 9, NULL, '2026-12-05', '22:00:00', 12000.00, 22000.00, 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `theater_id` int(11) NOT NULL,
  `theater_name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `total_seats` int(11) DEFAULT 100,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`theater_id`, `theater_name`, `location`, `total_seats`, `latitude`, `longitude`) VALUES
(1, 'Nueplex Cinemas', 'Rashid Minhas Road, Karachi', 220, 24.8995649, 67.1167719),
(2, 'Cinepax Ocean Mall', 'Clifton, Karachi', 180, 24.8239128, 67.0358475),
(3, 'Atrium Cinemas', 'I.I. Chundrigar Road, Karachi', 150, NULL, NULL),
(4, 'Mega Cineplex', 'Dolmen Mall Tariq Road, Karachi', 200, NULL, NULL),
(5, 'Nueplex Cinemas', 'Rashid Minhas Road, Karachi', 220, 24.9280000, 67.0794000),
(6, 'Cinepax Dolmen Mall Clifton', 'Clifton, Karachi', 200, 24.8138000, 67.0301000),
(7, 'Capri Cinema', 'Saddar, Karachi', 160, 24.8563000, 67.0099000),
(8, 'Bahria Town Cinema', 'Bahria Town, Karachi', 180, 24.8946000, 67.2635000),
(9, 'Askari IV Cinema', 'Askari IV, Karachi', 140, 24.8977000, 67.1330000),
(10, 'Cinepax Lucky One Mall', 'Rashid Minhas Road, Karachi', 210, 24.9271000, 67.0798000),
(11, 'Wonder Cinema Gulshan', 'Gulshan-e-Iqbal, Karachi', 150, 24.9200000, 67.0975000),
(12, 'Silver Screen Cinema', 'North Nazimabad, Karachi', 130, 24.9370000, 67.0424000),
(13, 'Space Cinema DHA', 'DHA Phase 6, Karachi', 170, 24.8090000, 67.0664000),
(14, 'Cinepax Fortress Square', 'Fortress Stadium, Lahore', 200, 31.4767000, 74.3568000),
(15, 'Cinepax MM Alam', 'MM Alam Road, Lahore', 180, 31.5085000, 74.3436000),
(16, 'Nueplex Cinemas Gulberg', 'Gulberg, Lahore', 190, 31.5105000, 74.3452000),
(17, 'DHA Cinema Lahore', 'DHA Phase 6, Lahore', 170, 31.4700000, 74.4200000),
(18, 'Cinestar Emporium Mall', 'Emporium Mall, Lahore', 230, 31.4805000, 74.2836000),
(19, 'Universal Cinemas', 'Packages Mall, Lahore', 210, 31.4780000, 74.3230000),
(20, 'Atrium Cinemas Mall Road', 'Mall Road, Lahore', 150, 31.5590000, 74.3300000),
(21, 'Sozo World Cinema', 'DHA Phase 5, Lahore', 160, 31.4600000, 74.4100000),
(22, 'Cinepax Centaurus Mall', 'F-8, Islamabad', 220, 33.7092000, 73.0551000),
(23, 'Nueplex Cinemas Giga Mall', 'Bahria Town, Islamabad', 200, 33.5700000, 73.1450000),
(24, 'Silver Screen Safa Gold', 'F-7, Islamabad', 140, 33.6690000, 73.0480000),
(25, 'Capri Cinema Blue Area', 'Blue Area, Islamabad', 130, 33.7107000, 73.0563000),
(26, 'Play Cinema F-7', 'F-7 Markaz, Islamabad', 120, 33.7180000, 73.0640000),
(27, 'Cinepax Al-Meraj', 'Bahria Town, Rawalpindi', 190, 33.5350000, 73.1000000),
(28, 'Nishtar Cinema', 'Committee Chowk, Rawalpindi', 140, 33.6007000, 73.0679000),
(29, 'Wonder Cinema Satellite Town', 'Commercial Market, Rawalpindi', 150, 33.6280000, 73.0551000),
(30, 'ChenOne Cinema Saddar', 'Saddar, Rawalpindi', 120, 33.5988000, 73.0479000),
(31, 'Cinepax Kohinoor City Mall', 'Kohinoor City Mall, Faisalabad', 200, 31.4460000, 73.1150000),
(32, 'Sitara Mall Cinema', 'Sitara Mall, Faisalabad', 140, 31.4187000, 73.0791000),
(33, 'Wonder Cinema D-Ground', 'D-Ground, Faisalabad', 130, 31.4230000, 73.0870000),
(34, 'Cinestar Multan', 'Cantt, Multan', 170, 30.1978000, 71.4697000),
(35, 'Wonder Cinema Multan', 'Officers Colony, Multan', 140, 30.1978000, 71.4900000),
(36, 'Emerald Cinema Multan', 'Emerald Tower, Multan', 150, 30.1610000, 71.5249000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `google_id` varchar(255) DEFAULT NULL,
  `picture` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `created_at`, `google_id`, `picture`) VALUES
(1, 'Mustafa Faisal', 'mustafarangeela786@gmail.com', NULL, '+92 335 3924555', '2026-08-14 09:33:36', '115390362535833817433', 'https://lh3.googleusercontent.com/a/ACg8ocJgUuOyeN_sDw8IXydIwz0-831iASS8w_b29oA19S3xmr5ceuYtVA=s96-c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `show_id` (`show_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`),
  ADD KEY `idx_show_seat` (`show_id`,`seat_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`screen_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD UNIQUE KEY `uniq_screen_seat` (`screen_id`,`seat_code`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`show_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `theater_id` (`theater_id`),
  ADD KEY `screen_id` (`screen_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`theater_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `screens`
--
ALTER TABLE `screens`
  MODIFY `screen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `show_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=669;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `theater_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `shows` (`show_id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `shows` (`show_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `screens`
--
ALTER TABLE `screens`
  ADD CONSTRAINT `screens_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE;

--
-- Constraints for table `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shows_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shows_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
