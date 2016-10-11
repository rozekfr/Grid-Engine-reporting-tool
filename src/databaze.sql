-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Úte 17. kvě 2016, 21:08
-- Verze serveru: 5.7.9
-- Verze PHP: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `vut_bakalarka`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `admini`
--

DROP TABLE IF EXISTS `admini`;
CREATE TABLE IF NOT EXISTS `admini` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `heslo` char(40) COLLATE utf8_czech_ci NOT NULL,
  `posledni_prihlaseni` datetime DEFAULT NULL,
  `superadmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `cekajici_ulohy`
--

DROP TABLE IF EXISTS `cekajici_ulohy`;
CREATE TABLE IF NOT EXISTS `cekajici_ulohy` (
  `id_ulohy` int(11) NOT NULL,
  `uzivatel` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `cas_odeslani` datetime NOT NULL,
  `stav` varchar(5) COLLATE utf8_czech_ci NOT NULL,
  `pocet_tasku` int(11) NOT NULL,
  PRIMARY KEY (`id_ulohy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `konfigurace`
--

DROP TABLE IF EXISTS `konfigurace`;
CREATE TABLE IF NOT EXISTS `konfigurace` (
  `nazev` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `mb` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `cpu` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `pocet_slotu` int(11) NOT NULL,
  `ram` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `hdd` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `gpu` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `net` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `info` text COLLATE utf8_czech_ci NOT NULL,
  `prikon_uzlu` int(11) DEFAULT NULL,
  `prikon_gpu` int(11) DEFAULT NULL,
  PRIMARY KEY (`nazev`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rozpis_uzivatele_skupiny`
--

DROP TABLE IF EXISTS `rozpis_uzivatele_skupiny`;
CREATE TABLE IF NOT EXISTS `rozpis_uzivatele_skupiny` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uzivatel` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `id_skupiny` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `sge_rt_stats_ulohy`
--

DROP TABLE IF EXISTS `sge_rt_stats_ulohy`;
CREATE TABLE IF NOT EXISTS `sge_rt_stats_ulohy` (
  `id_ulohy` int(11) NOT NULL,
  `uzivatel` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `pocet_tasku` int(11) NOT NULL,
  `pocet_gpu` int(11) NOT NULL,
  `cas_startu` timestamp NOT NULL,
  `realny_cas` decimal(16,6) NOT NULL,
  `cpu_cas` decimal(16,6) NOT NULL,
  `prum_cas_na_task` decimal(16,6) NOT NULL,
  `efektivita` decimal(6,2) DEFAULT NULL,
  `spotreba` decimal(16,6) NOT NULL,
  `alokovana_pamet_MB` decimal(16,6) DEFAULT NULL,
  `vyuzita_pamet_MB` decimal(16,6) DEFAULT NULL,
  `max_vyuzita_pamet_MB` decimal(16,6) NOT NULL,
  PRIMARY KEY (`id_ulohy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `skupiny`
--

DROP TABLE IF EXISTS `skupiny`;
CREATE TABLE IF NOT EXISTS `skupiny` (
  `nazev` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `info` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`nazev`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `uzivatele`
--

DROP TABLE IF EXISTS `uzivatele`;
CREATE TABLE IF NOT EXISTS `uzivatele` (
  `uzivatel` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `jmeno` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `vychozi_skupina` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `sge` tinyint(1) NOT NULL DEFAULT '0',
  `aktivita` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uzivatel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `uzly`
--

DROP TABLE IF EXISTS `uzly`;
CREATE TABLE IF NOT EXISTS `uzly` (
  `nazev` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `id_konfigurace` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`nazev`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
