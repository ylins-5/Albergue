-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/11/2025 às 05:46
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `albergue`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `beds`
--

CREATE TABLE `beds` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `beds`
--

INSERT INTO `beds` (`id`, `quarto_id`, `numero`, `criado_em`) VALUES
(4, 7, 1, '2025-11-24 00:46:54'),
(5, 7, 2, '2025-11-24 00:46:56'),
(6, 7, 3, '2025-11-24 00:46:58'),
(7, 7, 4, '2025-11-24 00:47:00'),
(8, 7, 5, '2025-11-24 00:47:02'),
(9, 7, 6, '2025-11-24 00:47:04'),
(10, 7, 7, '2025-11-24 00:47:07'),
(11, 7, 8, '2025-11-24 00:47:10'),
(12, 8, 1, '2025-11-24 00:49:36'),
(13, 8, 2, '2025-11-24 00:49:39'),
(14, 8, 3, '2025-11-24 00:49:41'),
(15, 8, 4, '2025-11-24 00:49:43'),
(16, 8, 5, '2025-11-24 00:49:45'),
(17, 8, 6, '2025-11-24 00:49:47'),
(18, 8, 7, '2025-11-24 00:49:49'),
(19, 8, 8, '2025-11-24 00:49:50'),
(20, 7, 9, '2025-11-24 01:40:39'),
(21, 7, 10, '2025-11-24 01:40:44'),
(22, 7, 11, '2025-11-24 01:40:46'),
(23, 7, 12, '2025-11-24 01:40:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bed_id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `user_id`, `bed_id`, `data_inicio`, `data_fim`, `created_at`, `updated_at`) VALUES
(1, 7, 10, '2025-12-18', '2025-12-20', '2025-11-24 01:51:44', '2025-11-24 01:51:44'),
(2, 7, 6, '2025-12-18', '2025-12-21', '2025-11-24 01:52:44', '2025-11-24 01:52:44'),
(3, 10, 23, '2025-12-24', '2025-12-26', '2025-11-24 02:32:03', '2025-11-24 02:32:03'),
(4, 10, 4, '2025-12-18', '2025-12-20', '2025-11-24 02:50:48', '2025-11-24 02:50:48'),
(5, 10, 4, '2025-11-25', '2025-11-28', '2025-11-24 02:52:43', '2025-11-24 02:52:43'),
(6, 10, 4, '2025-11-28', '2025-12-02', '2025-11-24 03:23:11', '2025-11-24 03:23:11'),
(7, 10, 4, '2025-12-13', '2025-12-15', '2025-11-24 03:42:36', '2025-11-24 03:42:36'),
(8, 10, 9, '2026-02-24', '2026-02-27', '2025-11-24 04:04:38', '2025-11-24 04:04:38'),
(9, 10, 11, '2026-01-24', '2026-01-29', '2025-11-24 04:24:53', '2025-11-24 04:24:53'),
(10, 11, 20, '2026-03-11', '2026-03-13', '2025-11-24 04:40:20', '2025-11-24 04:40:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo` enum('individual','duplo','dormitorio') DEFAULT 'dormitorio',
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rooms`
--

INSERT INTO `rooms` (`id`, `numero`, `tipo`, `descricao`, `criado_em`, `imagem`) VALUES
(7, 'Quarto Nascer do Sol', 'dormitorio', 'É um espaçoso alojamento projetado para acomodar até 12 pessoas, combinando conforto e praticidade. Com quatro beliches e quatro camas individuais, oferece flexibilidade para grupos variados, desde famílias a viajantes individuais. Equipado com ar condicionado para maior conforto em qualquer estação, o quarto também possui um banheiro privativo que proporciona conveniência adicional. A disposição inteligente dos móveis otimiza o espaço, garantindo uma estadia confortável e relaxante para todos os hóspedes, enquanto a luz do sol da manhã cria uma atmosfera luminosa e acolhedora.', '2025-11-24 00:46:48', 'http://localhost/albergue/public/uploads/rooms/room_6923aaf86096c.jpg'),
(8, 'Quarto Premium', 'dormitorio', 'Este quarto acomoda confortavelmente até 8 pessoas, com quatro camas de casal. Equipado com ar-condicionado, é ideal para proporcionar uma estadia agradável. Situado na área mais afastada da propriedade, garante maior privacidade e tranquilidade. A ausência de sol direto nas janelas mantém o ambiente sempre fresco e agradável.', '2025-11-24 00:49:28', 'http://localhost/albergue/public/uploads/rooms/room_6923ab983d6a6.jpg'),
(9, 'Quarto Pôr do Sol ', 'dormitorio', 'É um espaço acolhedor e funcional com duas beliches, acomodando até quatro pessoas, ideal para grupos ou famílias. Recebe a luz do sol durante a tarde, criando uma atmosfera agradável e iluminada. O banheiro privativo oferece comodidade e privacidade, enquanto a disposição dos móveis maximiza o espaço. Equipado com ar condicionado para conforto em qualquer estação, é a escolha perfeita para quem busca um ambiente confortável, bem iluminado e climatizado para descansar e relaxar.', '2025-11-24 01:40:28', 'http://localhost/albergue/public/uploads/rooms/room_6923b78cc3b4a.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tag_cama`
--

CREATE TABLE `tag_cama` (
  `tag_id` int(11) NOT NULL,
  `cama_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tag_quarto`
--

CREATE TABLE `tag_quarto` (
  `tag_id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `papel` enum('admin','funcionario','hospede') DEFAULT 'hospede',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `documento` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `papel`, `criado_em`, `documento`) VALUES
(3, 'andre', 'anmdre@example.com', '$2y$10$CI4wIwaGdYJq5dPJ/QeWM.eDwNuJ6Taj5jzkeRo7qrCRanE11cwJe', 'hospede', '2025-11-18 22:41:43', NULL),
(4, 'Yamal', 'lalala@example.com', '425363', 'hospede', '2025-11-18 23:02:48', 'ASBD2421'),
(5, 'Yuri Lins Gomes de Souza', 'teste@gmail.com', '$2y$10$hDt5b/54UktJqPR5H/rbau6Y/1p5kBN4ppryV/7PUtmNTNpXp2wD.', 'hospede', '2025-11-23 20:42:30', '123.456.789-99'),
(6, 'lins', 'teste2@gmail.com', '$2y$10$McFp0GI4ww9stPWQU8PIROWPEaw.NaL9R3km2hiiHw2rE/AtPg1rK', 'hospede', '2025-11-23 21:02:46', '111.222.333-33'),
(7, 'perdigazz', 'pedro@gmail.com', '$2y$10$kbbSg9stiwrYWGdklWDvEuPwcsjyyWMmk4sL/mimQyd7sex/YIHOu', 'hospede', '2025-11-23 21:47:04', '123.456.789-96'),
(10, 'gomes', 'gomes@gmail.com', '$2y$10$hc70gAvxj98ua9/He93VZ.WTumxdpoRGmJUyFxXpljEKeSPDP3N66', 'hospede', '2025-11-24 02:06:07', '123.456.789-98'),
(11, 'Daniel Amaral', 'daniel@gmail.com', '$2y$10$.kVwii3jg2rVGSfFVVUKaeRDeA3fti0HbmCBb3KU2NcUBB049U06S', 'hospede', '2025-11-24 04:39:09', '111.333.222-12');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_bed_id` (`bed_id`),
  ADD KEY `idx_dates` (`data_inicio`,`data_fim`);

--
-- Índices de tabela `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tag_cama`
--
ALTER TABLE `tag_cama`
  ADD PRIMARY KEY (`tag_id`,`cama_id`),
  ADD KEY `cama_id` (`cama_id`);

--
-- Índices de tabela `tag_quarto`
--
ALTER TABLE `tag_quarto`
  ADD PRIMARY KEY (`tag_id`,`quarto_id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`);

--
-- Restrições para tabelas `tag_cama`
--
ALTER TABLE `tag_cama`
  ADD CONSTRAINT `tag_cama_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_cama_ibfk_2` FOREIGN KEY (`cama_id`) REFERENCES `beds` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tag_quarto`
--
ALTER TABLE `tag_quarto`
  ADD CONSTRAINT `tag_quarto_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_quarto_ibfk_2` FOREIGN KEY (`quarto_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
