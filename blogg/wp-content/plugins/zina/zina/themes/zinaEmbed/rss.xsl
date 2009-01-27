<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" 
	xmlns:atom="http://www.w3.org/2005/Atom" 
	xmlns:xhtml="http://www.w3.org/1999/xhtml" 
	xmlns:rss="http://purl.org/rss/1.0/">
	<xsl:output method="html" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"/>
	<xsl:variable name="feedurl" select="/rss/channel/link[@rel='self']/@href"/>
	<xsl:variable name="cssurl" select="/rss/cssurl"/>

	<xsl:template match="/rss">
	<html>
		<head>
			<title><xsl:value-of select="/rss/channel/title"/></title>
			<link href="{$cssurl}" rel="stylesheet" type="text/css" media="all"/>
		</head>

		<body>
			<p>You're viewing an XML content feed which is
				intended to be viewed within a feed aggregator.</p>
			<h3>Subscribe to <xsl:value-of select="/rss/channel/title"/></h3>
			<ul>
				<li><a href="http://add.my.yahoo.com/rss?url={$feedurl}">Yahoo</a></li>
				<li><a href="http://fusion.google.com/add?feedurl={$feedurl}">Google</a></li>
			</ul>

			<xsl:apply-templates select="/rss/channel" />

			<div class="item"><ul>
			<xsl:apply-templates select="/rss/channel/item" />
			</ul></div>
		</body>
	</html>
	</xsl:template>

	<xsl:template match="channel">
		<xsl:variable name="link" select="link[@rel='alternate']/@href"/>
		<div class="channel"><h1><a href="{$link}"><xsl:value-of select="/rss/channel/title"/></a></h1>
		<p>
		<xsl:if test="image">
			<xsl:apply-templates select="/rss/channel/image" />
		</xsl:if>
		<xsl:value-of select="/rss/channel/description"/></p>
		</div>
	</xsl:template>

	<xsl:template match="image">
			<xsl:variable name="link" select="link"/>
			<xsl:variable name="url" select="url"/>
			<a href="{$url}"><img src="{$link}"/></a>
	</xsl:template>
	
	<xsl:template match="item">
  		<xsl:variable name="link" select="link"/>
		<li><a href="{$link}"><xsl:value-of select="title"/></a><br/>
			<xsl:value-of select="description"/>
		</li>
	</xsl:template>

</xsl:stylesheet>
