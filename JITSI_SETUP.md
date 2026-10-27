# Jitsi Meet Integration Setup

The platform has been migrated from BigBlueButton to Jitsi Meet for video conferencing.

## Configuration

Add these environment variables to your `.env` file:

```env
# Jitsi Meet Configuration
JITSI_DOMAIN=meet.jit.si
JITSI_USE_JWT=false
JITSI_APP_ID=
JITSI_APP_SECRET=
JITSI_JWT_TTL=3600
```

## Options

### Option 1: Use Public Jitsi Server (Easiest)

Use the free public Jitsi server at `meet.jit.si`:

```env
JITSI_DOMAIN=meet.jit.si
JITSI_USE_JWT=false
```

**Pros:**
- No setup required
- Free to use
- Instant availability

**Cons:**
- No control over branding
- Subject to public server limitations
- No authentication by default

### Option 2: Self-Hosted Jitsi (Recommended for Production)

Host your own Jitsi server:

1. **Install Jitsi on your server:**
   ```bash
   # Quick install on Ubuntu/Debian
   wget -qO - https://download.jitsi.org/jitsi-key.gpg.key | sudo apt-key add -
   sudo sh -c "echo 'deb https://download.jitsi.org stable/' > /etc/apt/sources.list.d/jitsi-stable.list"
   sudo apt update
   sudo apt install jitsi-meet
   ```

2. **Configure your domain:**
   ```env
   JITSI_DOMAIN=meet.yourdomain.com
   JITSI_USE_JWT=true
   JITSI_APP_ID=your_app_id
   JITSI_APP_SECRET=your_secret_key
   ```

3. **Enable JWT authentication** (optional but recommended):
   - Edit `/etc/jitsi/meet/meet.yourdomain.com-config.js`
   - Enable JWT authentication
   - Generate app ID and secret key
   - Update your `.env` file

**Pros:**
- Full control over branding
- Custom configurations
- Better privacy
- No participant limits

**Cons:**
- Requires server setup
- Maintenance responsibility
- Server costs

### Option 3: Jitsi as a Service (jaas.8x8.vc)

Use Jitsi's official managed service:

```env
JITSI_DOMAIN=8x8.vc
JITSI_USE_JWT=true
JITSI_APP_ID=your_jaas_app_id
JITSI_APP_SECRET=your_jaas_api_key
```

Sign up at: https://jaas.8x8.com/

**Pros:**
- Managed infrastructure
- Better reliability than public server
- Authentication included
- Custom branding available

**Cons:**
- Paid service (has free tier)
- Still dependent on third party

## JWT Authentication (Optional)

If using JWT authentication (`JITSI_USE_JWT=true`), you need:

1. **Install JWT package:**
   ```bash
   composer require firebase/php-jwt
   ```

2. **Configure your Jitsi server for JWT**
3. **Set APP_ID and APP_SECRET in `.env`**

## Features

### What Works:

- ✅ One-click join for tutors and students
- ✅ Automatic moderator privileges for tutors
- ✅ Recording capabilities (for moderators)
- ✅ Screen sharing
- ✅ Chat functionality
- ✅ No downloads required (browser-based)
- ✅ Mobile support
- ✅ End-to-end encryption

### Differences from BigBlueButton:

| Feature | BigBlueButton | Jitsi |
|---------|---------------|-------|
| Meeting Creation | API call required | Instant (no pre-creation) |
| Passwords | Required | Optional (UUID-based security) |
| Waiting Room | Yes | No (direct join) |
| Recording | Built-in | Moderator-controlled |
| Breakout Rooms | Yes | No |
| Polling | Yes | No |
| Whiteboard | Yes | Via integrations |

## Security

Jitsi meetings are secured by:
1. **Unique UUIDs** in room names (hard to guess)
2. **Session codes** (TUT-XXXXXX and STU-XXXXXX) verified before joining
3. **JWT authentication** (if enabled)
4. **Moderator controls** (tutors can remove participants)

## Testing

Test your Jitsi integration:

1. Create a booking
2. Verify session codes are generated
3. Click "Join Live Session"
4. Confirm video room loads
5. Test as both tutor (moderator) and student

## Troubleshooting

### Issue: "Failed to load Jitsi"

**Solution:** Check that `JITSI_DOMAIN` is correct and accessible

### Issue: "No audio/video permissions"

**Solution:** Ensure browser has microphone/camera permissions

### Issue: "JWT token invalid"

**Solution:** Verify `JITSI_APP_ID` and `JITSI_APP_SECRET` are correct

### Issue: "Room not loading"

**Solution:** Check browser console for errors, verify firewall settings

## Migration Notes

- **Database**: No migrations needed - existing `meeting_room` column used
- **Passwords**: `meeting_password` and `moderator_password` columns are now unused (set to NULL)
- **URLs**: Join URLs are generated on-the-fly (not stored)
- **Backward Compatibility**: Old `getBBBJoinUrl()` method aliased to Jitsi

## Support

- Jitsi Documentation: https://jitsi.github.io/handbook/
- Community Forum: https://community.jitsi.org/
- Self-Hosting Guide: https://jitsi.github.io/handbook/docs/devops-guide/devops-guide-quickstart
