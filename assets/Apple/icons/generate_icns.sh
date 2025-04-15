#!/bin/bash

# 📁 Répertoires
SRC_IMG="sequoia.png"
ICONSET_DIR="icon.iconset"
OUT_ICNS="icon.icns"

# 🧹 Nettoyage s'il existe déjà
rm -rf $ICONSET_DIR $OUT_ICNS
mkdir $ICONSET_DIR

# 📐 Génération des tailles
sips -z 16 16     $SRC_IMG --out $ICONSET_DIR/icon_16x16.png
sips -z 32 32     $SRC_IMG --out $ICONSET_DIR/icon_16x16@2x.png
sips -z 32 32     $SRC_IMG --out $ICONSET_DIR/icon_32x32.png
sips -z 64 64     $SRC_IMG --out $ICONSET_DIR/icon_32x32@2x.png
sips -z 128 128   $SRC_IMG --out $ICONSET_DIR/icon_128x128.png
sips -z 256 256   $SRC_IMG --out $ICONSET_DIR/icon_128x128@2x.png
sips -z 256 256   $SRC_IMG --out $ICONSET_DIR/icon_256x256.png
sips -z 512 512   $SRC_IMG --out $ICONSET_DIR/icon_256x256@2x.png
sips -z 512 512   $SRC_IMG --out $ICONSET_DIR/icon_512x512.png
cp $SRC_IMG                $ICONSET_DIR/icon_512x512@2x.png

# 🧪 Génération finale du .icns
iconutil -c icns $ICONSET_DIR -o $OUT_ICNS

# ✅ Confirmation
echo "✅ Fichier $OUT_ICNS généré avec succès."