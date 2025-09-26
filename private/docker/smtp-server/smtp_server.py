#!/usr/bin/env python3
"""
Modern SMTP test server with OAuth2 XOAUTH2 authentication support
Uses aiosmtpd instead of deprecated smtpd
"""

import asyncio
import base64
import logging
from aiosmtpd.controller import Controller
from aiosmtpd.smtp import SMTP as SMTPServer
from aiosmtpd.smtp import Envelope
from email.message import EmailMessage

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)


class OAuth2SMTPHandler:
    """Handler for processing messages received by SMTP server"""
    
    async def handle_RCPT(self, server, session, envelope, address, rcpt_options):
        """Handle RCPT TO command"""
        logger.info(f"RCPT TO: {address}")
        envelope.rcpt_tos.append(address)
        return '250 OK'

    async def handle_DATA(self, server, session, envelope):
        """Handle message data"""
        logger.info(f"Message received from {envelope.mail_from} to {envelope.rcpt_tos}")
        logger.info(f"Message size: {len(envelope.content)} bytes")
        
        # Log first 200 chars of message for debugging
        content_preview = envelope.content.decode('utf-8', errors='ignore')[:200]
        logger.info(f"Content preview: {content_preview}...")
        
        return '250 Message accepted for delivery'


class OAuth2SMTP(SMTPServer):
    """Custom SMTP server with OAuth2 XOAUTH2 support"""
    
    def __init__(self, handler, *args, **kwargs):
        super().__init__(handler, *args, **kwargs)
        self.authenticated_sessions = set()
        
    async def smtp_AUTH(self, arg):
        """Handle AUTH command with OAuth2 support"""
        logger.info(f"AUTH command received: {arg[:50]}...")
        
        if not arg:
            await self.push('501 Syntax error in parameters or arguments')
            return
            
        parts = arg.split(' ', 1)
        mechanism = parts[0].upper()
        
        if mechanism == 'XOAUTH2':
            await self._handle_xoauth2_auth(parts[1] if len(parts) > 1 else '')
        elif mechanism == 'PLAIN':
            await self._handle_plain_auth(parts[1] if len(parts) > 1 else '')
        else:
            logger.warning(f"Unsupported AUTH mechanism: {mechanism}")
            await self.push('504 Authentication mechanism not supported')
    
    async def _handle_xoauth2_auth(self, auth_string):
        """Handle XOAUTH2 authentication"""
        try:
            if not auth_string:
                await self.push('334 ')  # Request auth string
                response = await self.reader.readline()
                auth_string = response.decode().strip()
            
            logger.info("Processing XOAUTH2 authentication")
            
            # Decode base64 auth string
            try:
                decoded = base64.b64decode(auth_string).decode('utf-8')
                logger.info(f"Decoded XOAUTH2 string: {decoded[:100]}...")
                
                # Parse OAuth2 string: user=email\x01auth=Bearer token\x01\x01
                if 'user=' in decoded and 'auth=Bearer' in decoded:
                    parts = decoded.split('\x01')
                    user_part = next((p for p in parts if p.startswith('user=')), None)
                    auth_part = next((p for p in parts if p.startswith('auth=Bearer')), None)
                    
                    if user_part and auth_part:
                        username = user_part.split('=', 1)[1]
                        token = auth_part.split('Bearer ', 1)[1]
                        
                        logger.info(f"OAuth2 authentication for user: {username}")
                        logger.info(f"Token received (first 20 chars): {token[:20]}...")
                        
                        # For testing, accept any token
                        self.authenticated_sessions.add(id(self.session))
                        await self.push('235 Authentication successful')
                        logger.info("OAuth2 authentication successful!")
                        return
                
                logger.warning("Invalid XOAUTH2 format")
                await self.push('535 Authentication failed - invalid format')
                
            except Exception as e:
                logger.error(f"Failed to decode XOAUTH2 string: {e}")
                await self.push('535 Authentication failed - decode error')
                
        except Exception as e:
            logger.error(f"XOAUTH2 authentication error: {e}")
            await self.push('535 Authentication failed')
    
    async def _handle_plain_auth(self, auth_string):
        """Handle PLAIN authentication (for fallback testing)"""
        logger.info("Processing PLAIN authentication")
        # For testing, accept any plain auth
        self.authenticated_sessions.add(id(self.session))
        await self.push('235 Authentication successful')
        logger.info("PLAIN authentication successful!")
    
    async def smtp_MAIL(self, arg):
        """Handle MAIL FROM command - require authentication"""
        session_id = id(self.session)
        
        if session_id not in self.authenticated_sessions:
            logger.warning("MAIL FROM attempted without authentication")
            await self.push('530 Authentication required')
            return
            
        logger.info(f"MAIL FROM: {arg}")
        return await super().smtp_MAIL(arg)
    
    async def smtp_EHLO(self, hostname):
        """Extended HELO with AUTH capabilities"""
        await super().smtp_EHLO(hostname)
        await self.push('250-AUTH XOAUTH2 PLAIN LOGIN')
        await self.push('250 HELP')


def main():
    """Start the OAuth2 SMTP test server"""
    host = '0.0.0.0'
    port = 587
    
    logger.info("Starting OAuth2 SMTP test server...")
    logger.info(f"Server will listen on {host}:{port}")
    logger.info("Supported AUTH mechanisms: XOAUTH2, PLAIN, LOGIN")
    
    handler = OAuth2SMTPHandler()
    
    controller = Controller(
        OAuth2SMTP(handler),
        hostname=host,
        port=port
    )
    
    try:
        controller.start()
        logger.info(f"OAuth2 SMTP server running on {host}:{port}")
        logger.info("Press Ctrl+C to stop the server")
        
        # Keep the server running
        asyncio.get_event_loop().run_forever()
        
    except KeyboardInterrupt:
        logger.info("Shutting down server...")
    finally:
        controller.stop()


if __name__ == '__main__':
    main()