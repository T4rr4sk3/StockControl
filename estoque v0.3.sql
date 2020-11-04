-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04-Nov-2020 às 16:51
-- Versão do servidor: 10.4.14-MariaDB
-- versão do PHP: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `estoque`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `controle`
--

CREATE TABLE `controle` (
  `id` int(10) UNSIGNED NOT NULL,
  `qtde` int(10) UNSIGNED NOT NULL,
  `estado` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT 'Novo',
  `id_marca` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `modelo` varchar(30) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `controle`
--

INSERT INTO `controle` (`id`, `qtde`, `estado`, `id_marca`, `id_tipo`, `modelo`) VALUES
(1, 5, 'Novo', 1, 1, 'Comum USB'),
(2, 10, 'Novo', 2, 1, 'Comum USB'),
(3, 10, 'Novo', 3, 2, 'Comum 1.3m USB'),
(4, 5, 'Em Uso', 3, 2, 'Comum 1.3m USB'),
(5, 8, 'Usado', 1, 1, 'Comum USB'),
(6, 1, 'Em Uso', 1, 1, 'Comum USB'),
(7, 10, 'Usado', 3, 2, 'Comum 1.3m USB'),
(8, 5, 'Em Uso', 2, 1, 'Comum USB'),
(9, 3, 'Novo', 4, 3, 'VGA HD 1366x768'),
(10, 4, 'Em Uso', 4, 3, 'VGA HD 1366x768'),
(11, 5, 'Novo', 5, 1, 'Comum USB'),
(12, 1, 'Em Uso', 5, 1, 'Comum USB'),
(13, 3, 'Novo', 6, 2, 'M90 USB'),
(14, 1, 'Em Uso', 6, 2, 'M90 USB');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico`
--

CREATE TABLE `historico` (
  `usuario` varchar(50) COLLATE utf8_bin NOT NULL,
  `operacao` varchar(20) COLLATE utf8_bin NOT NULL,
  `qtde_op` int(10) NOT NULL,
  `qtde_dps` int(10) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `modelo` varchar(30) COLLATE utf8_bin NOT NULL,
  `estado` varchar(20) COLLATE utf8_bin NOT NULL,
  `dataehora` datetime NOT NULL,
  `chamado` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `requerente` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Nenhum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `historico`
--

INSERT INTO `historico` (`usuario`, `operacao`, `qtde_op`, `qtde_dps`, `id_tipo`, `id_marca`, `modelo`, `estado`, `dataehora`, `chamado`, `requerente`) VALUES
('DanielP', 'RETIRAR', 1, 3, 1, 5, 'Comum USB', 'Novo', '2020-10-16 18:16:36', 0, 'Nenhum'),
('DanielP', 'REPOR NOVO', 2, 5, 1, 5, 'Comum USB', 'Novo', '2020-10-16 18:17:53', 0, 'Nenhum'),
('Teste', 'RETIRAR', 1, 9, 1, 1, 'Comum USB', 'Novo', '2020-10-16 18:19:55', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 1, 3, 2, 6, 'M90 USB', 'Novo', '2020-10-16 18:22:54', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 1, 5, 3, 4, 'VGA HD 1366x768', 'Novo', '2020-10-16 18:54:02', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 1, 4, 3, 4, 'VGA HD 1366x768', 'Novo', '2020-10-16 18:54:12', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 3, 6, 1, 1, 'Comum USB', 'Novo', '2020-10-16 18:56:59', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 1, 5, 1, 1, 'Comum USB', 'Novo', '2020-10-16 18:57:09', 0, 'Nenhum'),
('DanielP', 'RETIRAR', 1, 1, 3, 4, 'VGA HD 1366x768', 'Novo', '2020-10-19 15:24:58', 0, 'Nenhum'),
('DanielP', 'REPOR NOVO', 2, 3, 3, 4, 'VGA HD 1366x768', 'Novo', '2020-10-19 16:00:51', 0, 'Nenhum');

-- --------------------------------------------------------

--
-- Estrutura da tabela `marca`
--

CREATE TABLE `marca` (
  `nome` varchar(25) COLLATE utf8_bin NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `marca`
--

INSERT INTO `marca` (`nome`, `id`) VALUES
('Itautec', 1),
('Hoopson', 2),
('BansonTech', 3),
('LG', 4),
('Lenovo', 5),
('Logitech', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo`
--

CREATE TABLE `tipo` (
  `nome` varchar(25) COLLATE utf8_bin NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `tipo`
--

INSERT INTO `tipo` (`nome`, `id`) VALUES
('Teclado', 1),
('Mouse', 2),
('Monitor', 3),
('', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `pass` varchar(20) NOT NULL,
  `nivel` int(1) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `username`, `pass`, `nivel`) VALUES
(1, 'DanielP', 'dfpelajo', 'daniel37571537', 5),
(2, 'Teste', 'teste', 'teste123', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `controle`
--
ALTER TABLE `controle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Índices para tabela `historico`
--
ALTER TABLE `historico`
  ADD KEY `fk_id_tipo` (`id_tipo`),
  ADD KEY `fk_id_marca` (`id_marca`);

--
-- Índices para tabela `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `controle`
--
ALTER TABLE `controle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `marca`
--
ALTER TABLE `marca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `controle`
--
ALTER TABLE `controle`
  ADD CONSTRAINT `controle_ibfk_1` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `controle_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `fk_id_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id`),
  ADD CONSTRAINT `fk_id_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
