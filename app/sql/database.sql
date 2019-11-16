-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 23, 2019 at 09:12 AM
-- Server version: 5.7.26
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `g8t8`
--
CREATE DATABASE IF NOT EXISTS `g8t8` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `g8t8`;

-- --------------------------------------------------------

--
-- Table structure for table `bid`
--

CREATE TABLE `bid` (
  `userid` varchar(128) NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  `cid` varchar(255) NOT NULL,
  `sid` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bid_result`
--

CREATE TABLE `bid_result` (
  `round` int(1) NOT NULL,
  `cid` varchar(255) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `ranking` int(11) NOT NULL,
  `userid` varchar(128) NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  `state` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `item` varchar(5) NOT NULL,
  `value` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `cid` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `examdate` date NOT NULL,
  `examstart` time NOT NULL,
  `examend` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `course_completed`
--

CREATE TABLE `course_completed` (
  `userid` varchar(128) NOT NULL,
  `cid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `minbid`
--

CREATE TABLE `minbid` (
  `cid` varchar(255) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `minbid` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

CREATE TABLE `prerequisite` (
  `cid` varchar(255) NOT NULL,
  `prerequisite` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `cid` varchar(255) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `dayweek` int(1) NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `userid` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `name` varchar(100) NOT NULL,
  `school` varchar(255) NOT NULL,
  `edollar` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bid`
--
ALTER TABLE `bid`
  ADD PRIMARY KEY (`userid`,`cid`);

--
-- Indexes for table `bid_result`
--
ALTER TABLE `bid_result`
  ADD PRIMARY KEY (`round`,`userid`,`cid`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`item`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `course_completed`
--
ALTER TABLE `course_completed`
  ADD PRIMARY KEY (`userid`,`cid`);

--
-- Indexes for table `minbid`
--
ALTER TABLE `minbid`
  ADD PRIMARY KEY (`cid`,`sid`);

--
-- Indexes for table `prerequisite`
--
ALTER TABLE `prerequisite`
  ADD PRIMARY KEY (`cid`,`prerequisite`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`cid`,`sid`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`userid`);
